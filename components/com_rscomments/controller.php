<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSCommentsController extends JController
{
	var $_db;
	
	function __construct()
	{
		parent::__construct();
		
		// Set the database object
		$this->_db =& JFactory::getDBO();
		//read the config
		RSCommentsHelper::readConfig();
		//load the captcha files
		require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'securimage'.DS.'securimage.php');
	}
	
	/**
	 * Display the view
	 */
	function display()
	{
		parent::display();
	}
	
	function captcha()
	{
		ob_end_clean();
		
		$captcha = new JSecurImage();
		
		$captcha_lines = RSCommentsHelper::getConfig('captcha_lines');
		if ($captcha_lines)
			$captcha->num_lines = 8;
		else
			$captcha->num_lines = 0;
		
		$captcha_characters = RSCommentsHelper::getConfig('captcha_chars');
		$captcha->code_length = $captcha_characters;
		$captcha->image_width = 30*$captcha_characters + 50;
		$captcha->show();
		
		die();
	}
	
	function pagination()
	{
		JRequest::setVar('tmpl','component');
		JRequest::setVar('limitstart',JRequest::getInt('limitstart',0));
		JRequest::setVar('pagination',0);
		$option		= JRequest::getCmd('content','');
		$id			= JRequest::getInt('id',0);
		$template	= JRequest::getCmd('rsctemplate','default');
		
		$override = JRequest::getInt('override');
		
		echo RSCommentsHelper::show($option,$id,$template,1,$override);
		die();
	}
	
	function terms()
	{
		JRequest::setVar('tmpl','component');
		$config = RSCommentsHelper::getConfig();
		
		if ($config->terms)
			echo $config->terms_message;
	}
	
	function upload()
	{
		JRequest::setVar('tmpl','component');
		
		echo '<form name="frameform" id="frameform" action="'.JRoute::_('index.php?option=com_rscomments&task=uploadfile').'" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file" />';
		echo '<input type="hidden" name="cid" value="0" id="cid" />';
		echo '</form>';
		die();
	}
	
	function uploadfile()
	{
		JRequest::setVar('tmpl','component');
		$db =& JFactory::getDBO();
		$file = JRequest::getVar('file', null, 'files', 'array');
		$cid = JRequest::getVar('cid');
		$config = RSCommentsHelper::getConfig();
		
		if (!$config->enable_upload) return;
		
		jimport('joomla.filesystem.file');
		$uploadFolder = JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'assets'.DS.'files'.DS;
		
		$refresh = false;
		
		if (!empty($file) && empty($file['error']))
		{
			$src		= $file['tmp_name'];
			$filename	= JFile::makeSafe($file['name']);
			$ext		= JFile::getExt($filename);
			$filename	= JFile::stripExt($filename);
			
			$extensions = $config->allowed_extensions;
			$extensions = str_replace("\r",'',$extensions);
			$extensions = explode("\n",$extensions);
			
			if (!empty($extensions) && is_array($extensions) && in_array($ext,$extensions))
			{
				$max_size = empty($config->max_size) ? 10 : $config->max_size;
				$max_size = $max_size * 1024 * 1024;
				
				if ($max_size > $file['size'])
				{				
					while (JFile::exists($uploadFolder.$filename.'.'. $ext))
						$filename .= rand(10, 99);
					
					$dest = $uploadFolder.$filename.'.'.$ext;
					JFile::upload($src, $dest);
					
					$refresh = true;
					
					$db->setQuery("UPDATE #__rscomments_comments SET `file` = '".$db->getEscaped($filename.'.'.$ext)."'  WHERE IdComment = ".$cid." ");
					$db->query();
				}
			}
		}
		
		echo '<form name="frameform" id="frameform" action="'.JRoute::_('index.php?option=com_rscomments&task=uploadfile').'" method="post" enctype="multipart/form-data">';
		echo '<input type="file" name="file" />';
		echo '<input type="hidden" name="cid" value="0" id="cid" />';
		echo '</form>';
		
		if ($refresh)
		{
			$u =& JFactory::getURI();
			$roor = $u->toString(array('scheme','host'));
			echo '<script type="text/javascript">window.parent.rsc_refresh(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments&task=refresh&cid='.$cid,false).'\');</script>';
		}
		exit();
	}
	
	
	function download()
	{
		JRequest::setVar('tmpl','component');
		$db =& JFactory::getDBO();
		$cid = JRequest::getInt('cid',0);
		
		if ($cid)
		{
			$db->setQuery("SELECT file FROM #__rscomments_comments WHERE IdComment = ".$cid." ");
			$file = $db->loadResult();
			
			
			$download_folder = JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'assets'.DS.'files';
			$fullpath = $download_folder.DS.$file;			
			if (strpos(realpath($fullpath), $download_folder) !== 0) JError::raiseError(500,JText::_('RSC_ACCESS_DENIED'));
			
			if(is_file($fullpath))
			{
				@ob_end_clean();
				$filename = basename($fullpath);
				header("Cache-Control: public, must-revalidate");
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header("Pragma: no-cache");
				header("Expires: 0"); 
				header("Content-Description: File Transfer");
				header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
				if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
					header("Content-Type: application/octetstream"); 
				else 
					header("Content-Type: application/octet-stream");
				header("Content-Length: ".(string) filesize($fullpath));
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary\n");
				RSCommentsController::readfile_chunked($fullpath);
				exit();
				
			} else JError::raiseError(500,JText::_('RSC_ACCESS_DENIED'));
		}
		
		exit();
	}
	
	function readfile_chunked($filename,$retbytes=true)
	{
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
	   $status = fclose($handle);
	   if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
	
	function refresh()
	{
		JRequest::setVar('tmpl','component');
		
		$db =& JFactory::getDBO();
		$cid = JRequest::getInt('cid',0);
		$override = JRequest::getInt('override');
		$config = RSCommentsHelper::getConfig();
		
		$db->setQuery("SELECT `id`, `option` FROM #__rscomments_comments WHERE IdComment = ".$cid." ");
		$comment = $db->loadObject();
		
		$class = new RSCommentsModelComments($comment->id,$comment->option,$config->nr_comments);
		$pagination = $class->getPagination();
		$last_page = $pagination->get('pages.stop');
		$limitstart = ($last_page -1) * $pagination->limit;
		JRequest::setVar('limitstart',$limitstart);
		JRequest::setVar('pagination',0);
		
		$template = RSCommentsHelper::getTemplate();
		$comments = RSCommentsHelper::show($comment->option,$comment->id,$template,1,$override);
		
		echo $comments;
		die();
	}
}
?>