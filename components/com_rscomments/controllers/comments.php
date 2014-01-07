<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSCommentsControllerComments extends RSCommentsController
{
	function __construct()
	{
		parent::__construct();
		
		// Comments Tasks
		$this->registerTask('publish', 'changestatus');
		$this->registerTask('unpublish', 'changestatus');
		$this->registerTask('voteup', 'changevote');
		$this->registerTask('votedown', 'changevote');
		$this->registerTask('subscribe', 'subscribe');
		$this->registerTask('unsubscribe', 'subscribe');
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rscomments'.DS.'tables');
	}
	
	//Comments - Edit
	function edit()
	{
		// Get the model
		$model = $this->getModel('comments');
		
		//Get permissions
		$permissions = $model->getPermissions();
		
		if(!isset($permissions['new_comments'])) return;
		
		$row = $model->getComment();
		$return = $row->IdComment . '{s3p}'.$row->name . '{s3p}'.$row->email . '{s3p}'.$row->subject . '{s3p}'.$row->website . '{s3p}'. $row->comment;
		echo $return;
	}
	
	//Comments - Edit
	function quote()
	{
		// Get the model
		$model = $this->getModel('comments');
		
		$row = $model->getComment();
		$return = $row->comment;
		echo $return;
	}
	
	//Comments - Subscribe
	function subscribe()
	{
		$u =& JFactory::getURI();
		
		//get root
		$root = $u->toString(array('scheme','host'));
		
		// Get the task
		$task = JRequest::getCmd('task');
		
		// Get the model
		$model = $this->getModel('comments');
		
		//Get the id
		$id = JRequest::getVar('cid');
		
		//Get the option 
		$option = JRequest::getVar('opt');
		
		
		$row = $task == 'subscribe' ?  $model->subscribe() : $model->unsubscribe();
		
		if($task == 'subscribe')
		{
			$msg = $row ? JText::_('RSC_SUBSCRIBED') : JText::_('RSC_ALREADY_SUBSCRIBED');
			$function = 'rsc_unsubscribe(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments',false).'\',\''.$id.'\',\''.$option.'\')';
			$text = JText::_('RSC_UNSUBSCRIBE');
		} else 
		{
			$msg = $row ? JText::_('RSC_UNSUBSCRIBED') : JText::_('RSC_ALREADY_UNSUBSCRIBED');
			$function = 'rsc_subscribe(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments',false).'\',\''.$id.'\',\''.$option.'\')';
			$text = JText::_('RSC_SUBSCRIBE');
		}		
		
		echo $msg.'|<a href="javascript:void(0);" onclick="'.$function.'"><img src="'.JURI::root().'components/com_rscomments/assets/images/subscribe.png" alt="'.$text.'" /> '.$text.'</a>';
	}
	
	
	// Comments - Remove
	function remove()
	{
		// Get the model
		$model = $this->getModel('comments');
		
		//get the config
		$config = RSCommentsHelper::getConfig();
		
		// Get the selected items
		$cid = JRequest::getInt('cid');
		
		// No items are selected
		if (empty($cid))
			JError::raiseWarning(500, JText::_('SELECT ITEM DELETE'));
		// Try to remove the item
		else
		{
			$model->remove($cid);			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rscomments');
			$cache->clean();
		}
	}
	
	// Comments - Save
	function save()
	{
		// Get the model
		$model = $this->getModel('comments');
		
		//get the config
		$config = RSCommentsHelper::getConfig();
		
		// Save
		$comment = $model->save();
		if($comment == false) return;
		
		if(is_array($comment)) 
		{
			echo 'err|';
			echo implode("\n",$comment);
			echo '|';
			echo implode("\n",array_keys($comment)); 
			exit(); 
		}
		
		$class = new RSCommentsModelComments($comment->id,$comment->option,$config->nr_comments);
		$pagination = $class->getPagination();
		$last_page = $pagination->get('pages.stop');
		$limitstart = ($last_page -1) * $pagination->limit;
		JRequest::setVar('limitstart',$limitstart);
		JRequest::setVar('pagination',0);
		
		//Permissions
		$permissions = $model->getPermissions();
		
		$template = RSCommentsHelper::getTemplate();
		
		$override = JRequest::getInt('override');
		
		$comm = $comment->IdComment . '|';
		$comm .= RSCommentsHelper::show($comment->option,$comment->id,$template,1,$override);
		
		//clean the cache
		$cache = & JFactory::getCache($comment->option);
		$cache->clean();
		
		$msg = !isset($permissions['autopublish']) ? JText::_('RSC_COMMENT_SAVED_UNPUBL') : JText::_('RSC_COMMENT_SAVED');
		
		$comm .= '<div id="rsc_message" class="rsc_message">'.$msg.' <a href="javascript:void(0)" onclick="document.getElementById(\'rsc_message\').style.display = \'none\';">Close</a></div>';
		echo $comm;
	}
	
	// Comments - Publish/Unpublish
	function changestatus()
	{
		$u =& JFactory::getURI();
		
		//get root
		$root = $u->toString(array('scheme','host'));
		
		// Get the model
		$model = $this->getModel('comments');
		
		// Get the selected items
		$cid = JRequest::getInt('cid');

		// Get the task
		$task = JRequest::getCmd('task');
		
		// No items are selected
		if (empty($cid))
			JError::raiseWarning(500, JText::_('SELECT ITEM PUBLISH'));
		// Try to publish the item
		else
		{
			$value = $task == 'publish' ? 1 : 0;
			if (!$model->publish($cid, $value))
				JError::raiseError(500, 'You don\'t have permission to publish/unpublish comments STOP IT!!');
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rscomments');
			$cache->clean();
		}
		
		$publish = ($value == 1) ? 'lighton' : 'lightoff'; 
		$function = ($value == 1) ? 'rsc_unpublish(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments',false).'\',\''.$cid.'\')' : 'rsc_publish(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments',false).'\',\''.$cid.'\')'; 
		$message = ($value == 1) ? JText::_('RSC_UNPUBLISH') : JText::_('RSC_PUBLISH');
		
		echo '<a href="javascript:void(0);" onclick="'.$function.'" title="'.$message.'"><img src="'.JURI::root().'components/com_rscomments/assets/images/'.$publish.'.png" alt="'.$message.'" /></a>';
	}
	
	// Comments - Vote
	function changevote()
	{		
		//get the db connection
		$db = JFactory::getDBO();
		
		// Get the model
		$model = $this->getModel('comments');
		
		// Get the selected items
		$cid = JRequest::getInt('cid');

		// Get the task
		$task = JRequest::getCmd('task');
		
		// No items are selected
		if (empty($cid))
			JError::raiseWarning(500, JText::_('SELECT ITEM VOTE'));
		// Try to publish the item
		else
		{
			$value = $task == 'voteup' ? 1 : 0;
			if (!$model->vote($cid, $value))
				JError::raiseError(500, $model->getError());
			
			// Clean the cache, if any
			$cache =& JFactory::getCache('com_rscomments');
			$cache->clean();
		}
		
		$db->setQuery("SELECT COUNT(`IdVote`) FROM `#__rscomments_votes` WHERE `IdComment` = '".$cid."' AND `value` = 'positive' ");
		$pos = $db->loadResult();
		$db->setQuery("SELECT COUNT(`IdVote`) FROM `#__rscomments_votes` WHERE `IdComment` = '".$cid."' AND `value` = 'negative' ");
		$neg = $db->loadResult();
		
		echo $pos - $neg;
	}
	
}
?>