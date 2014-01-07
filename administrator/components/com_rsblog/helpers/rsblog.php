<?php
/**
* @version 1.0.0
* @package RSBlog! 1.0.0
* @copyright (C) 2010-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

define('_RSBLOG_VERSION', '6');
define('_RSBLOG_VERSION_LONG', '1.0.0');
define('_RSBLOG_KEY', 'BL864YHJ41');
define('_RSBLOG_PRODUCT', 'RSBlog!');
define('_RSBLOG_COPYRIGHT', '&copy; 2010 RSJoomla!');
define('_RSBLOG_LICENSE', 'GPL Commercial License');
define('_RSBLOG_AUTHOR', '<a href="http://www.rsjoomla.com" target="_blank">www.rsjoomla.com</a>');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsblog'.DS.'tables');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsblog'.DS.'helpers'.DS.'html.php');

class RSBlogHelper
{
	
	//read the configuration
	function readConfig()
	{
		$db 			=& JFactory::getDBO();
		$session		=& JFactory::getSession();
		$rsblog_config 	= new stdClass();

		$db->setQuery("SELECT * FROM `#__rsblog_configuration`");
		$config = $db->loadObjectList();

		foreach ($config as $config_item)
			$rsblog_config->{$config_item->name} = $config_item->value;

		$session->set('rsblog_config', $rsblog_config);
	}
	
	//get configuration
	function getConfig($name = null)
	{
		$session =& JFactory::getSession();
		$config	 = $session->get('rsblog_config');
		
		if ($name != null) {
			if (isset($config->$name)) return $config->$name;
				else return false;
		}
		else return $config;
	}
	
	//check for joomla version
	function is16()
	{
		$jversion = new JVersion();
		$current_version =  $jversion->getShortVersion();
		return (version_compare('1.6.0', $current_version) <= 0);
	}
	
	//generate the rskey
	function genKeyCode()
	{
		$code = RSBlogHelper::getConfig('global_register_code');
		if ($code === false) $code = '';
		return md5($code._RSBLOG_KEY);
	}
	
	//get the list of categories
	function getCategories($name,$params,$selected,$options=null,$disabled=null,$type = 1)
	{
		$db =& JFactory::getDBO();
		
		if (RSBlogHelper::is16())
			$db->setQuery("SELECT id , title , parent as parent_id FROM #__rsblog_categories WHERE published = 1 ORDER BY id, parent, title");
		else
			$db->setQuery("SELECT id , title as name , parent FROM #__rsblog_categories WHERE published = 1 ORDER BY id, parent, title");
		$categories = $db->loadObjectList();
		
		if (!is_array($disabled) && !is_null($disabled)) $disabled = array($disabled);
		
		$children = array();
		$categs 	= array();
		
		if ( $categories )
		{
			// first pass - collect children
			foreach ( $categories as $v )
			{
				if ($v->id == 1)
				{
					if (RSBlogHelper::is16())
						$v->title = JText::_('RSB_UNCATEGORIZED');
					else 
						$v->name = JText::_('RSB_UNCATEGORIZED');
				}
				$pt 	= RSBlogHelper::is16() ? $v->parent_id : $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		
		if ($type)
			$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		else 
			$list = JHTML::_('rsblog.tree', 0, '', array(), $children, 9999, 0 );
			
		if ($options)
			$categs[] = $options;
		
		if ($type)
		{
			foreach ( $list as $item )
			{
				$published = false;
				if (!is_null($disabled))
					$published = (!in_array($item->id,$disabled)) ? true : false;
				$categs[] = JHTML::_('select.option',  $item->id, '   '. $item->treename, 'value' , 'text' , $published );
			}
		} else 
		{		
			foreach ($list as $item)
				$categs[] = JHTML::_('select.option',  $item->id, $item->treename);
		}
		
		if ($type)
		{
			if (RSBlogHelper::is16())
			{
				$opts = array(
					'list.attr' 		 => $params,
					'option.text.toHtml' => false,
					'option.key'		 => 'value',
					'option.text'		 => 'text',
					'list.select'		 => $selected
				);
				return JHTML::_('select.genericlist', $categs, $name , $opts);
			} 
			return JHTML::_('select.genericlist', $categs, $name , $params, 'value', 'text', $selected );
		}
		else 
			return JHTML::_('rsblog.checklist', $categs, $name, $params, true, 'value', 'text', $selected);
	}
	
	//get the list of created groups
	function getGroups($selected = null,$everyone = 1)
	{
		$db =& JFactory::getDBO();
		
		$db->setQuery("SELECT `id`,`title` FROM `#__rsblog_groups` ORDER BY `id` ASC");
		$groups = $db->loadObjectList();
		
		if ($everyone)
		{
			$option[] = JHTML::_('select.option', 0, JText::_('RSB_EVERYONE'),'id','title');
			$groups = array_merge($option,$groups);
		}
		
		
		if (!is_array($selected))
			$selected = explode(',',$selected);
		
		return JHTML::_('select.genericlist', $groups, 'groups[]', 'class="inputbox" size="8" multiple="multiple"', 'id', 'title', $selected );
	}
	
	//add the categories children to the category list
	function addChildren($id, &$list)
	{
		$db =& JFactory::getDBO();
		
		// Initialize variables
		$return = true;

		// Get all rows with parent of $id
		$query = 'SELECT id' .
				' FROM #__rsblog_categories' .
				' WHERE parent = '.(int) $id;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Make sure there aren't any errors
		if ($db->getErrorNum()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Recursively iterate through all children... kinda messy
		// TODO: Cleanup this method
		foreach ($rows as $row)
		{
			$found = false;
			foreach ($list as $idx)
			{
				if ($idx == $row->id) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$list[] = $row->id;
			}
			$return = RSBlogHelper::addChildren($row->id, $list);
		}
		return $return;
	}
	
	//prepare content for saving
	function saveContentPrep(&$row, $data, $user_id=null)
	{
		//get the configuration
		$config = RSBlogHelper::getConfig();

		// Clean text for xhtml transitional compliance
		$data['text'] = str_replace('<br>', '<br />', $data['text']);

		// Search for the {readmore} tag and split the text up accordingly.
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos	= preg_match($pattern, $data['text']);

		if ( $tagPos == 0 )
			$row->introtext	= $data['text'];
		else
			list($row->introtext, $row->fulltext) = preg_split($pattern, $data['text'], 2);

		// Filter settings
		$user	=& JFactory::getUser($user_id);
		
		if (RSBlogHelper::is16())
		{
			$gid = 'no gid';
			$groups = JAccess::getGroupsByUser($user->id);
			if (!$user->guest)
				foreach ($groups as $i => $group)
					if ($group == 1) unset($groups[$i]);			
		} else 
			$gid	= $user->get( 'gid' );

		$filterGroups	=  $config->be_posts_filtergroups;
		
		//convert to array
		if (!empty($filterGroups) && strlen($filterGroups) > 1)
			$filterGroups = explode(',',$filterGroups);
			
		
		// convert to array if one group selected
		if ( (!is_array($filterGroups) && (int) $filterGroups > 0) ) { 
			$filterGroups = array($filterGroups);
		}

		$apply = false;
		if (RSBlogHelper::is16())
		foreach ($groups as $group)
			if (in_array($group,$filterGroups))
				$apply = true;
		
		if (is_array($filterGroups) && (in_array($gid,$filterGroups) || $apply) )
		{
			$filterType		= $config->be_posts_filtertype;
			$filterTags		= preg_split( '#[,\s]+#', trim( $config->be_posts_filtertags ) );
			$filterAttrs	= preg_split( '#[,\s]+#', trim( $config->be_posts_filterattributes ) );
			switch ($filterType)
			{
				case 'NH':
					$filter	= new JFilterInput();
				break;
				case 'WL':
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 0, 0);  // turn off xss auto clean
				break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
				break;
			}
			$row->introtext	= $filter->clean( $row->introtext );
			$row->fulltext	= $filter->clean( $row->fulltext );
		} elseif(empty($filterGroups) && $gid != '25') { // no default filtering for super admin (gid=25)
			$filter = new JFilterInput( array(), array(), 1, 1 );
			$row->introtext	= $filter->clean( $row->introtext );
			$row->fulltext	= $filter->clean( $row->fulltext );
		}
		
		return true;
	}
	
	
	//get comment systems
	function getComponents()
	{
		$db =& JFactory::getDBO();

		$supported_components = array ( "RSComments" => "com_rscomments", "JComments" => "com_jcomments", "Jom Comment" => 'com_jomcomment' );
		$installed_components = array();

		foreach ($supported_components as $name => $item) 
		{
			$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.$item;
			if(file_exists($path))
			{
				if (RSBlogHelper::is16())
					$db->setQuery("SELECT `enabled` FROM #__extensions WHERE `type` = 'component' AND `element`='".$item."' LIMIT 1");
				else
					$db->setQuery("SELECT `enabled` FROM #__components WHERE `name`='".$name."' LIMIT 1");
				$is_enabled = $db->loadResult();
				
				if ($is_enabled)
					$installed_components[$name] = $item;
			}
		}
		
		if (count($installed_components)==0)
			$installed_components = null;

		return $installed_components;
	}
	
	//set scripts
	function setScripts($client = 'administrator')
	{
		$doc =& JFactory::getDocument();
		
		if ($client == 'administrator')
		{
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rsblog/assets/css/rsblog.css');
			$doc->addScript	(JURI::root(true).'/administrator/components/com_rsblog/assets/js/scripts.js');
		} else 
		{
			$doc->addStyleSheet(JURI::root(true).'/components/com_rsblog/assets/css/style.css');
			$doc->addScript(JURI::root(true).'/components/com_rsblog/assets/js/rsblog.js');
			$doc->addScript(JURI::root(true).'/includes/js/joomla.javascript.js');
		}
		
		return true;
	}
	
	//clean rsblog cache if any
	function cleancache()
	{
		$cache =& JFactory::getCache('com_rsblog');
		$cache->clean();
	}
	
	//archive posts
	function autoArchive()
	{
		$db =& JFactory::getDBO();
		
		//get the interval
		$interval = RSBlogHelper::getConfig('auto_archive_posts');
		
		if (!$interval) return true;
		
		$last_check = RSBlogHelper::getConfig('auto_archive_last_check');
		$check_interval = RSBlogHelper::getConfig('auto_archive_check_interval');
		$now = time();
		if ($last_check + ($check_interval*60) > $now)
			return true;
		
		$db->setQuery("UPDATE #__rsblog_configuration SET `value`='".$now."' WHERE `name`='auto_archive_last_check'");
		$db->query();
		
		$interval = 86400 * $interval;
		
		$db->setQuery("SELECT `id` FROM `#__rsblog_posts` WHERE ".$now." > `created_date` + ".$interval." AND `archived` = 0 ");
		$ids = $db->loadResultArray();
		
		if (!empty($ids))
		{
			JArrayHelper::toInteger($ids);
			$ids = implode(',',$ids);
		
			$db->setQuery("UPDATE `#__rsblog_posts` SET `published` = '-1' , `archived` = 1 WHERE `id` IN (".$ids.") ");
			$db->query();
		}
		
		return true;
	}
	
	//check if one comment system is enabled
	function comments()
	{
		$config = RSBlogHelper::getConfig();
		
		if (in_array($config->integration_commenting,array('com_rscomments','com_jcomments','com_jomcomment'))) return true;
		return false;
	}
	
	//create date format
	function displayDate($timestamp, $return_unix = false, $show_time = true, $show_date = true)
	{
		$config_blog = RSBlogHelper::getConfig();
		$config_global = new JConfig();
		$format = '';
		
		$date =& JFactory::getDate($timestamp, -$config_global->offset);
		$date = $date->toUnix();
		if ( $return_unix )
			return $date;
		else
		{
			if ( $show_date ) $format .= $config_blog->date_format ;
			if ( $show_time ) $format .= ' '. $config_blog->time_format ;
			
			return RSBlogHelper::translate(date($format, $date));
		}
	}
	
	//get the total number of comments per topic
	function getComments($id)
	{
		$db =& JFactory::getDBO();
		$config = RSBlogHelper::getConfig();
		$result = '';
		
		switch ($config->integration_commenting) 
		{
			case 'com_rscomments':
				$db->setQuery("SELECT COUNT(IdComment) FROM #__rscomments_comments WHERE `option`='com_rsblog' AND `id`='".$id."'");
				$result = $db->loadResult();
			break;
			
			case 'com_jcomments':
				$db->setQuery("SELECT COUNT(id) FROM #__jcomments WHERE `object_group`='com_rsblog' AND `object_id`='".$id."'");
				$result = $db->loadResult();
			break;
			
			case 'com_jomcomment':
				$db->setQuery("SELECT COUNT(id) FROM #__jomcomment WHERE `option`='com_rsblog' AND `contentid`='".$id."'");
				$result = $db->loadResult();
			break;
		}
		
		return $result;
	}
	
	//get the link for the selected commenting system
	function commentLink()
	{
		$config = RSBlogHelper::getConfig();
		$return = '';
		
		switch ($config->integration_commenting) 
		{
			case 'com_rscomments':
				$return = ' <span class="hasTip" title="RSComments!"><a href="'.JRoute::_('index.php?option=com_rscomments&view=comments').'" alt="RSComments!" target="_blank">'.JText::_('RSB_POST_EDIT_VIEW_COMMENTS_LINK').'</a></span>';
			break;
			
			case 'com_jcomments':
				$return = ' <span class="hasTip" title="JComments"><a href="'.JRoute::_('index.php?option=com_jcomments&task=comments').'" alt="JComments" target="_blank">'.JText::_('RSB_POST_EDIT_VIEW_COMMENTS_LINK').'</a></span>';
			break;
			
			case 'com_jomcomment':
				$return = ' <span class="hasTip" title="JomComment"><a href="'.JRoute::_('index2.php?option=com_jomcomment&task=comments').'" alt="JomComment" target="_blank">'.JText::_('RSB_POST_EDIT_VIEW_COMMENTS_LINK').'</a></span>';
			break;
		}
		
		return $return;
	}
	
	//insert tags
	function setTags($tags,$id)
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$config = RSBlogHelper::getConfig();
		$id		= intval($id);
		
		if (empty($tags)) return;
		
		$filterTags		= preg_split( '#[,\s]+#', trim($config->be_posts_filtertags));
		$filterAttrs	= preg_split( '#[,\s]+#', trim($config->be_posts_filterattributes));
		
		foreach ($tags as $tag)
		{
			$tag = base64_decode($tag);
			if (empty($tag))
				continue;
				
			$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
			$tag	= $filter->clean( $tag );
			
			$db->setQuery("SELECT `id` FROM `#__rsblog_tags` WHERE `title` = '".$db->getEscaped($tag)."' LIMIT 1");
			$tagid = $db->loadResult();
			
			if ($tagid)
			{
				$db->setQuery("INSERT INTO `#__rsblog_posts_tags` SET `post_id`='".$id."', `tag_id`='".$tagid."'");
				$db->query();
				$db->setQuery("UPDATE `#__rsblog_tags` SET `times_used`= `times_used` + 1 WHERE `id` ='".$tagid."'");
				$db->query();
			}
			else 
			{
				$db->setQuery("INSERT INTO `#__rsblog_tags` SET `title` = '".$db->getEscaped($tag)."', `alias` = '".JFilterOutput::stringURLSafe($tag)."' , `created_by` = '".$user->id."' , `published`='1' , `times_used`='1' ");
				$db->query();
				$tid = $db->insertid();
				$db->setQuery("INSERT INTO `#__rsblog_posts_tags` SET `post_id`='".$id."',`tag_id`='".$tid."'");
				$db->query();
			}
		}
		return true;
	}
	
	//email parser
	function parser()
	{
		$config = RSBlogHelper::getConfig();
		if ($config->email_check_handling == 'manual' || $config->email_check_handling == 'both')
		{
			$now = time();
			if ($config->email_parser_last_check + ($config->email_parser_check_interval*60) > $now)
				return true;
			
			$db =& JFactory::getDBO();
			$db->setQuery("UPDATE #__rsblog_configuration SET `value`='".$now."' WHERE `name`='email_parser_last_check'");
			$db->query();
			
			$connect = new RSBlogConnect();
			$connect->parse();
		}
		return true;
	}
	
	//get permissions
	function getPermissions($permission = null)
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();		
		
		$uid  = $user->id;
		if (RSBlogHelper::is16())
		{
			$groups = JAccess::getGroupsByUser($user->id);
			if (!$user->guest)
				foreach ($groups as $i => $group)
					if ($group == 1) unset($groups[$i]);
		} else 
		{
			$ugid = ($user->gid == 0) ? 29 : $user->gid;
			$groups = array($ugid);
		}
		
		$permissions = new stdClass();
		
		//set defaults
		$permissions->canview = 1;
		$permissions->canpost = 0;
		$permissions->moderation = 0;
		$permissions->captcha = 0;
		$permissions->view_unpublished = 0;
		$permissions->edit_own = 0;
		$permissions->edit_all = 0;
		$permissions->delete_own = 0;
		$permissions->delete_all = 0;
		
		//get all groups that have this users id
		$db->setQuery("SELECT id , users_array FROM #__rsblog_groups WHERE users_array LIKE '%".$uid."%'");
		$uobject = $db->loadObject();
		
		if (!empty($uobject->users_array))
		{
			$uarray = explode(',',$uobject->users_array);
			if (in_array($uid,$uarray))
			{
				$db->setQuery("SELECT canview, canpost, moderation, view_unpublished, captcha, edit_own, edit_all, delete_own, delete_all FROM #__rsblog_groups WHERE id = '".$uobject->id."' ");
				$uperms = $db->loadObject();
				
				$permissions->canview = $uperms->canview;
				$permissions->canpost = $uperms->canpost;
				$permissions->moderation = $uperms->moderation;
				$permissions->captcha = $uperms->captcha;
				$permissions->view_unpublished = $uperms->view_unpublished;
				$permissions->edit_own = $uperms->edit_own;
				$permissions->edit_all = $uperms->edit_all;
				$permissions->delete_own = $uperms->delete_own;
				$permissions->delete_all = $uperms->delete_all;
			}
		}
		
		
		foreach ($groups as $group)
		{
			$db->setQuery("SELECT id, access_array FROM #__rsblog_groups WHERE access_array LIKE '%".$group."%'");
			$gobject = $db->loadObject();
			
			if (!empty($gobject->access_array))
			{
				$garray = explode(',',$gobject->access_array);
				if (in_array($group,$garray))
				{
					$db->setQuery("SELECT canview, canpost, moderation, captcha, edit_own, view_unpublished, edit_all, delete_own, delete_all FROM #__rsblog_groups WHERE id = '".$gobject->id."' ");
					$gperms = $db->loadObject();
					
					if (RSBlogHelper::is16())
					{
						foreach ($gperms as $perm => $value)
							if ($value) $permissions->{$perm} = $value;
					} else 
					{
						$permissions->canview = $gperms->canview;
						$permissions->canpost = $gperms->canpost;
						$permissions->moderation = $gperms->moderation;
						$permissions->captcha = $gperms->captcha;
						$permissions->view_unpublished = $gperms->view_unpublished;
						$permissions->edit_own = $gperms->edit_own;
						$permissions->edit_all = $gperms->edit_all;
						$permissions->delete_own = $gperms->delete_own;
						$permissions->delete_all = $gperms->delete_all;
					}
				}
			}
		}
		
		if (is_null($permission))
			$return = $permissions;
		else 
		{
			if (isset($permissions->{$permission}))
				$return = $permissions->{$permission};
			else $return = 0;
		}
		
		return $return;
	}
	
	//custom route function
	function route($url, $xhtml=true, $Itemid='')
	{
		if (strpos($url, 'Itemid=') === false)
		{
			if (!$Itemid)
			{
				$Itemid = JRequest::getInt('Itemid');
				if ($Itemid)
					$Itemid = 'Itemid='.$Itemid;
			}
			elseif ((int) ($Itemid))
			$Itemid = 'Itemid='.(int) $Itemid;

			if ($Itemid)
			$url .= (strpos($url, '?') === false) ? '?'.$Itemid : '&'.$Itemid;
		}

		return JRoute::_($url, $xhtml);
	}
	
	//check for post id
	function checkpost()
	{
		$db  =& JFactory::getDBO();
		$cid = JRequest::getInt('cid');
		
		$db->setQuery("SELECT COUNT(id) FROM #__rsblog_posts WHERE id = '".$cid."' ");
		return $db->loadResult();
	}
	
	//small sef function
	function sef($id,$name)
	{
		return $id.':'.JFilterOutput::stringURLSafe($name);
	}
	
	//hits counter
	function hits()
	{
		$db		=& JFactory::getDBO();
		$cid	= JRequest::getInt('cid');
		
		if ($cid)
		{
			$db->setQuery("UPDATE #__rsblog_posts SET `hits` = `hits` + 1 WHERE `id`='".$cid."'");
			$db->query();
		}
		return true;
	}
	
	//tag cloud generator
	function tagcloud($tags,$minmax,$params = array()) 
	{
		$mainframe =& JFactory::getApplication();
		
		$return = array();
		if (isset($params['module']))
		{
			$max_size = 20; // max font size in pixels
			$min_size = 14; // min font size in pixels
		} else
		{
			$max_size = 32; // max font size in pixels
			$min_size = 14; // min font size in pixels
		}

		// largest and smallest array values
		$max_qty = $minmax->max;
		$min_qty = $minmax->min;

		// find the range of values
		$spread = $max_qty - $min_qty;
		if ($spread == 0) $spread = 1;

		// set the font-size increment
		$step = ($max_size - $min_size) / ($spread);
		
		//opener
		if (isset($params['links']))
		{
			$opener = $params['links'] ? 'target="_blank"' : 'target="_self"';
		} else $opener = '';
		
		//itemid
		$itemid = isset($params['itemid']) ? $params['itemid'] : '';
			
		
		// loop through the tag array
		foreach ($tags as $tag) 
		{
			$module = isset($params['module']) ? '_module' : '';
			$size = round($min_size + (($tag->times_used - $min_qty) * $step));
			if ($mainframe->isAdmin())
				$return[] ='<a href="index.php?option=com_rsblog&view=tags&layout=edit&cid='.$tag->id.'" class="rsb_tag hasTip" style="font-size: ' . $size . 'px" title="' .JText::sprintf('RSB_DASHBOARD_TAGS_TITLE',$tag->title,$tag->title,$tag->times_used). '">' . $tag->title . '</a> ';
			else 
				$return[] = '<a '.$opener.' href="'.RSBlogHelper::route('index.php?option=com_rsblog&layout=default&tag='.RSBlogHelper::sef($tag->id,$tag->alias),false,$itemid).'" class="rsb_tag'.$module.'" style="font-size: ' . $size . 'px" >'.$tag->title.'</a>';
		}
		
		shuffle($return);
		
		return implode(' ',$return);
	}
	
	
	//show comments
	function getCommentsBlock($post)
	{
		if (RSBlogHelper::comments())
		{
			switch (RSBlogHelper::getConfig('integration_commenting')) 
			{
				//RSComments
				case 'com_rscomments':
					if (file_exists(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php')) 
					{
						require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php');
						echo '{rscomments option="com_rsblog" id="'.$post->id.'"}';
					}
				break;

				//JComments
				case 'com_jcomments':
					if (file_exists(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php')) 
					{
						require_once(JPATH_SITE.DS.'components'.DS.'com_jcomments'.DS.'jcomments.php');
						echo JComments::showComments($post->id, 'com_rsblog', $post->title);
					}
				break;

				//JomComment
				case 'com_jomcomment':
					if (file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php')) 
					{
						require_once(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jom_comment_bot.php');
						echo jomcomment($post->id, 'com_rsblog');
					}
				break;
			}
		}
		return true;
	}
	
	//get the current user group id
	function getgroup()
	{
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		
		$groupid = array();
		$uid = $user->id;
		
		if (RSBlogHelper::is16())
		{
			$groups = JAccess::getGroupsByUser($uid);
			if (!$user->guest)
				foreach ($groups as $i => $group)
					if ($group == 1) unset($groups[$i]);
		} else 
		{
			$gid = $user->gid == 0 ? 29 : $user->gid;
			$groups = array($gid); 
		}
		
		$db->setQuery("SELECT id , users_array FROM #__rsblog_groups WHERE users_array LIKE '%".$uid."%'");
		$uobject = $db->loadObject();
		
		if (!empty($uobject->users_array))
		{
			$uarray = explode(',',$uobject->users_array);
			if (in_array($uid,$uarray))
				$groupid[] = $uobject->id;
		}
		
		foreach ($groups as $group)
		{
			$db->setQuery("SELECT id, access_array FROM #__rsblog_groups WHERE access_array LIKE '%".$group."%'");
			$gobject = $db->loadObject();
			
			if (!empty($gobject->access_array))
			{
				$garray = explode(',',$gobject->access_array);
				if (in_array($group,$garray))
					$groupid[] = $gobject->id;
			}
		}		
		return $groupid;
	}
	
	//get a list of all the categories the current user can view 
	function usercategories($categories)
	{
		$db =& JFactory::getDBO();
		
		//split the categories into ids
		$categories = explode(',',$categories);
		JArrayHelper::toInteger($categories);
		//the current user groups ids
		$groupids = RSBlogHelper::getgroup();
		
		$db->setQuery("SELECT `id`,`access` FROM `#__rsblog_categories` WHERE `id` IN (".implode(',', $categories).")");
		$categories = $db->loadObjectList();
		
		$return = array();
		foreach ($categories as $i => $category)
		{
			$access = $category->access;
			if (!empty($access))
			{
				$access = explode(',',$access);
				if (!empty($access))
				{
					$continue = true;
					if (!empty($groupids))
						foreach ($groupids as $groupid)
							if (!in_array($groupid,$access) && !in_array(0,$access))
								$continue = false;
					
					if (!$continue) continue;
				}
			}
			
			$return[] = $category->id;
		}
		
		if (empty($return))
			$return[] = '-1';
		
		return implode(',', $return);
	}
	
	//check if the user has permission to view this post 
	function checkpostcategory($id,$layout)
	{
		$db =& JFactory::getDBO();
		
		//get categories
		if ($layout == 'view')
		{
			$db->setQuery("SELECT `cat_id` FROM `#__rsblog_posts_categories` WHERE `post_id` = '".$id."' ");
			$categories = $db->loadResultArray();
		} else $categories = array($id);
		
		//the current user groups ids
		$groupids = RSBlogHelper::getgroup();
		
		if (!empty($categories))
			foreach ($categories as $i => $category)
			{
				$db->setQuery("SELECT `access` FROM `#__rsblog_categories` WHERE `id` = '".$category."' ");
				$access = $db->loadResult();
				
				if (!empty($access))
				{
					$access = explode(',',$access);
					if (!empty($access))
					{
						$continue = true;
						if (!empty($groupids))
							foreach ($groupids as $groupid)
								if (!in_array($groupid,$access) && !in_array(0,$access))
									$continue = false;
						
						if (!$continue)
							unset($categories[$i]);
					}
				}
			}
		
		if (count($categories) > 0) return false;
		return true;
	}
	
	
	//add trackbacks to database
	function addTrackbacks($id)
	{
		$db =& JFactory::getDBO();
		
		$db->setQuery("SELECT `trackbacks` FROM `#__rsblog_posts` WHERE `id` = '".(int) $id."' ");
		$trackbacks = $db->loadResult();
		
		if (!empty($trackbacks))
		{
			$trackbacks = str_replace("\r",'',$trackbacks);
			$trackbacks = explode("\n",$trackbacks);
			
			if (!empty($trackbacks))
				foreach ($trackbacks as $trackback)
				{
					$trackback = trim($trackback);
					if (empty($trackback)) continue;
					$db->setQuery("INSERT INTO `#__rsblog_send_trackbacks` SET `post_id` = '".(int) $id."' , `trackback_url` = '".$db->getEscaped($trackback)."' , `type` = 'trackback' ");
					$db->query();
				}
		}
		
		return true;
	}
	
	//add pingbacks
	function addPingbacks($id)
	{
		$db =& JFactory::getDBO();
		
		$db->setQuery("SELECT CONCAT(`introtext`,`fulltext`) FROM `#__rsblog_posts` WHERE `id` = '".(int) $id."' ");
		$text = $db->loadResult();
		
		$links = array();

		$pattern = '#href(?:[ \t\r\n]+)?=(?:[ \t\r\n]+)?"([http|https].*?)(?:[ \t\r\n]+)?"#i';
		if (preg_match_all($pattern, $text, $matches))
			$links = array_merge($links, $matches[1]);
			
		$pattern = '#href(?:[ \t\r\n]+)?=(?:[ \t\r\n]+)?\'([http|https].*?)(?:[ \t\r\n]+)?\'#i';
		if (preg_match_all($pattern, $text, $matches))
			$links = array_merge($links, $matches[1]);

		if ($links)
		{
			$links = array_unique($links);
			$unallowed = '#(\.jpg|\.gif|\.css|\.js|\.png|\.mp3|\.swf|\.zip|\.tgz|\.tar|\.gz|\.rar|\.docx?|\.xls|\.bmp|\.pdf)#i';
			foreach ($links as $i => $link)
			{
				if (preg_match($unallowed, $link) || JURI::isInternal($link)) // || strpos($link, '://localhost/') || strpos($link, '://127.0.0.1/')
					continue;
					
				$links[$i] = RSBlogHelper::entityDecode($link);
				
				// Add to db
				$db->setQuery("INSERT INTO `#__rsblog_send_trackbacks` SET `post_id` = '".(int) $id."' , `trackback_url` = '".$db->getEscaped($link)."' , `type` = 'pingback' ");
				$db->query();
			}
		}
		
		return true;
	}
	
	function addTwitter($id)
	{
		$db =& JFactory::getDBO();
		$db->setQuery("INSERT INTO `#__rsblog_send_trackbacks` SET `post_id` = '".(int) $id."' , `trackback_url` = '".RSBlogHelper::getConfig('twitter_oauth_token_secret')."' , `type` = 'twitter' ");
		$db->query();
		
		return true;
	}
	
	function addFacebook($id)
	{
		$db =& JFactory::getDBO();
		$db->setQuery("INSERT INTO `#__rsblog_send_trackbacks` SET `post_id` = '".(int) $id."' , `trackback_url` = '".RSBlogHelper::getConfig('facebook_secret')."' , `type` = 'facebook' ");
		$db->query();
		
		return true;
	}
	
	//decode function
	function entityDecode($string)
	{
		if (function_exists('html_entity_decode'))
			return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
		
		$html 	= array('&amp;', '&gt;', '&lt;', '&quot;');
		$nohtml = array('&', '>', '<', '"');
		
		return str_replace($html, $nohtml, $string);
	}
	
	//fopen function
	function fopen($url,$int=0)
	 {
		//global $option,$mainframe;
		$option = JRequest::getCmd('option');
		$mainframe =& JFactory::getApplication();
		
		$u =& JURI::getInstance('SERVER');
		$base = $u->getHost();
		
		$errors = array();
		$url_info = parse_url($url);
		if($url_info['host'] == 'localhost') $url_info['host'] = '127.0.0.1';
		$useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)";
		$data = false;

		$url = html_entity_decode($url);
		
		// cURL
		if (extension_loaded('curl'))
		{
			// Init cURL
			$ch = @curl_init();
			
			// Set options
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, $int);
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			@curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			
			// Set timeout
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			
			// Grab data
			$data = @curl_exec($ch);
			
			$objs = explode("\n",$data);
			foreach($objs as $obj)
				if(strpos($obj,'Location:') !== false)
				{
					$new_url = trim(str_replace('Location: ','',$obj));
					if(strpos($new_url,$base) !== false) $data = rsseoHelper::fopen($new_url,0);
				}
			
			$curl_error = curl_error($ch);
			
			// Clean up
			@curl_close($ch);
			
			if(empty($data)) $errors[] = 'cURL';
			
			// Return data
			if ($data !== false)
				return $data;
		}
		

		// fsockopen
		if (function_exists('fsockopen'))
		{
			$errno = 0;
			$errstr = '';

			// Set timeout
			$fsock = @fsockopen($url_info['host'], 80, $errno, $errstr, 5);
		
			if ($fsock)
			{
				@fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
				@fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
				@fputs($fsock, "User-Agent: ".$useragent."\n");
				@fputs($fsock, 'Connection: close'."\r\n\r\n");
        
				// Set timeout
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				
				$data = '';
				$passed_header = false;
				while (!@feof($fsock))
				{
					if ($passed_header)
						$data .= @fread($fsock, 1024);
					else
					{
						if (@fgets($fsock, 1024) == "\r\n")
							$passed_header = true;
					}
				}
				
				// Clean up
				@fclose($fsock);
				
				if(empty($data)) $errors[] = 'fsockopen';
				
				// Return data
				if ($data !== false)
					return $data;
			}
		}

	 	// fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen'))
		{
			// Set timeout
			if (ini_get('default_socket_timeout') < 5)
				ini_set('default_socket_timeout', 5);
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			@ini_set('user_agent',$useragent);
			
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			
			$handle = @fopen ($url, 'r');
			
			if ($handle)
			{
				$data = '';
				while (!feof($handle))
					$data .= @fread($handle, 8192);
			
				// Clean up
				@fclose($handle);
			
				if(empty($data)) $errors[] = 'fopen';
			
				// Return data
				if ($data !== false)
					return $data;
			}
		}
						
		// file_get_contents
		if(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		{
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			@ini_set('user_agent',$useragent);
			$data = @file_get_contents($url);
			
			if(empty($data)) $errors[] = 'file_get_contents';
			
			// Return data
			if ($data !== false)
				return $data;
		}
		
		return $data;
	}
	
	function shorten($text, $length = 255, $ending = '', $exact = false, $considerHtml = true) 
	{
		if ($considerHtml) 
		{
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) 
				return $text;
				
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			
			foreach ($lines as $line_matchings) 
			{
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) 
				{
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) 
					{
						// do nothing
						// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) 
					{
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) 
						{
							unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) 
					{
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) 
				{
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) 
					{
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) 
						{
							if ($entity[1]+1-$entities_length <= $left) 
							{
								$left--;
								$entities_length += strlen($entity[0]);
							} else 
							{
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else 
				{
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				
				// if the maximum length is reached, get off the loop
				
				if($total_length>= $length) 
				{
					break;
				}
			}
		} else 
		{
			if (strlen($text) <= $length) 
				return $text;
			else
				$truncate = substr($text, 0, $length - strlen($ending));
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) 
		{
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) 
			{
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) 
		{
			// close all unclosed html-tags
			foreach ($open_tags as $tag) 
			{
				$truncate .= '</' . $tag . '>';
			}
		}
		
		$truncate = str_replace(array('<div/>','<div />','<span/>','<span />'),'',$truncate);
		return $truncate;
	}
	
	function savePost($data, $user_id=null)
	{
		$row      	=& JTable::getInstance('RSBlog_Posts','Table');
		$db	      	=& JFactory::getDBO();
		$config   	=& JFactory::getConfig();
		$blogconfig = RSBlogHelper::getConfig();
		
		// Get date settings
		$tzoffset = $config->getValue('config.offset');
		$date 	  =& JFactory::getDate('now', -$tzoffset);
		$current  = $date->toUnix();
		
		// Get the user
		$user =& JFactory::getUser($user_id);
		
		// Categories & Tags
		$data['categories'] = isset($data['categories']) && is_array($data['categories']) ? $data['categories'] : array(1);
		$data['tags'] 		= isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : array();
		
		if (!$row->bind($data))
			return JError::raiseWarning(500, $row->getError());
		
		// Set title if its empty
		if (empty($row->title))
			$row->title = JText::_('RSB_UNTITLED');
		
		// Create the alias
		$row->alias = empty($row->alias) ? JFilterOutput::stringURLSafe($row->title) : JFilterOutput::stringURLSafe($row->alias);
		
		// Created date
		$created_date =& JFactory::getDate($row->created_date, $tzoffset);
		$row->created_date = empty($row->created_date) ? $current : $created_date->toUnix();
		
		// Modified ?
		if ($row->id)
			$row->modified_date = $current;
		
		if (empty($row->publish_up) || $row->publish_up == $db->getNullDate())
			$row->publish_up = $db->getNullDate();
		else
		{
			// Append time if not added to publish date
			if (strlen(trim($row->publish_up)) <= 10)
				$row->publish_up .= ' 00:00:00';
			
			$publish_up =& JFactory::getDate($row->publish_up, $tzoffset);
			$row->publish_up = $publish_up->toMySQL();
		}
		
		if (empty($row->publish_down) || $row->publish_down == $db->getNullDate())
			$row->publish_down = $db->getNullDate();
		else 
		{
			// Append time if not added to publish date
			if (strlen(trim($row->publish_down)) <= 10)
				$row->publish_down .= ' 00:00:00';
			
			$publish_down =& JFactory::getDate($row->publish_down, $tzoffset);
			$row->publish_down = $publish_down->toMySQL();
		}
		
		// Author
		$row->created_by = $user->get('id');
		
		// Prepare the content for saving to the database
		RSBlogHelper::saveContentPrep($row, $data, $user_id);
		
		// Set ordering
		if (empty($row->id))
			$row->ordering = $row->getNextOrder();
		
		// Store to DB
		if ($row->store())
		{
			jimport('joomla.mail.helper');
			
			// Delete old categories and old tags
			$db->setQuery("SELECT `tag_id` FROM #__rsblog_posts_tags WHERE post_id= '".$row->id."'");
			$oldtags = $db->loadResultArray();
			if (!empty($oldtags))
			{
				JArrayHelper::toInteger($oldtags);
				$db->setQuery("UPDATE #__rsblog_tags SET `times_used` = `times_used` - 1 WHERE id IN (".implode(',',$oldtags).")");
				$db->query();
			}
			
			$db->setQuery("DELETE FROM #__rsblog_posts_categories WHERE post_id='".$row->id."'");
			$db->query();
			$db->setQuery("DELETE FROM #__rsblog_posts_tags WHERE post_id='".$row->id."'");
			$db->query();
			
			// Add categories
			foreach ($data['categories'] as $category)	
			{
				$db->setQuery("INSERT INTO `#__rsblog_posts_categories` SET `post_id`='".$row->id."', `cat_id`='".(int) $category."'");
				$db->query();
			}

			// Add tags
			if (!empty($data['tags']))
				RSBlogHelper::setTags($data['tags'],$row->id);
			
			$itemid = RSBlogHelperRoute::getPostRoute();
				
			// Build article link
			$article_link = RSBlogHelper::route(JURI::root().'index.php?option=com_rsblog&layout=view&cid='.RSBlogHelper::sef($row->id,$row->alias),true,$itemid);
			
			// Send subscription emails
			$db->setQuery("SELECT mails_sent FROM #__rsblog_posts WHERE `id`='".$row->id."'");
			$mails_sent = $db->loadResult();
			if ($blogconfig->subscriptions && $row->published && !$mails_sent)
			{
				$db->setQuery("UPDATE #__rsblog_posts SET `mails_sent`='1' WHERE `id`='".$row->id."'");
				$db->query();
				
				$lang =& JFactory::getLanguage();
				$lang->load('com_rsblog', JPATH_SITE);
				
				$from	  = $config->getValue('config.mailfrom');
				$fromName = $config->getValue('config.fromname');
				$subject  = JText::sprintf('RSB_SUBJECT_NEW_POST_ADDED', $blogconfig->blog_title);
				
				$categories = $data['categories'];
				JArrayHelper::toInteger($categories);
				$categories = empty($categories) ? array(1) : $categories;
				
				$db->setQuery("SELECT * FROM `#__rsblog_subscriptions` WHERE `confirmed`='1'");
				$subscribers = $this->_db->loadObjectList();
				foreach ($subscribers as $subscriber)
				{
					if (!empty($subscriber->category) && !in_array($subscriber->category,$categories)) continue;
					
					if (JMailHelper::isEmailAddress($subscriber->email))
					{
						$unsubscribe_link = RSBlogHelper::route(JURI::root().'index.php?option=com_rsblog&task=unsubscribe&code='.md5($subscriber->email.' '.$subscriber->id), $itemid);
						$body = JText::sprintf('RSB_BODY_NEW_POST_ADDED',$subscriber->name,$blogconfig->blog_title,$article_link,$unsubscribe_link,$unsubscribe_link);
						JUtility::sendMail($from , $fromName , $subscriber->email , $subject , $body , 1);
					}
				}
			}
			
			if ($blogconfig->notifications_newpost)
			{
				$notification_emails = $blogconfig->notifications_newpost_emails;
				$notification_emails = explode(',',$notification_emails);
				
				if (!empty($notification_emails))
				{
					foreach ($notification_emails as $email)
					{
						if (JMailHelper::isEmailAddress($email))
						{
							$from	  = $config->getValue('config.mailfrom');
							$fromName = $config->getValue('config.fromname');
							$subject  = JText::sprintf('RSB_SUBJECT_NEW_POST_ADDED', $blogconfig->blog_title);
							$body = JText::sprintf('RSB_BODY_NEW_POST_ADDED_NOTIFICATION',$blogconfig->blog_title,$article_link);
							
							JUtility::sendMail($from , $fromName , $email , $subject , $body , 1);
						}
					}
				}
			}

			// JomSocial
			$type  = RSBlogHelper::getConfig('userdetails');
			$owner = $type ? $user->get('name') : $user->get('username');
			$blog_link = RSBlogHelper::route('index.php?option=com_rsblog&layout=default&author='.RSBlogHelper::sef($row->created_by,$owner), true, $itemid);
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger('onRSBlogPostEdit', array($user, $row, $blog_link, $article_link));

			if(file_exists(JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php')){
				require_once( JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');

				$lang =& JFactory::getLanguage();
				$lang->load('com_rsblog',JPATH_ADMINISTRATOR);
				
				$isNew = $row->modified_date ? false : true;

				$post_link = '<a href="'.$article_link.'">'.$row->title.'</a>';
				$blog_link = '<a href="'.$blog_link.'">'.JText::_('blog').'</a>';

				$act = new stdClass();
				$act->cmd     = 'rsblog.editpost';
				$act->actor   = $row->created_by;
				$act->target  = 0; // no target
				$act->title   = sprintf(($isNew) ? JText::_('RSB_COMMUNITY_ACTOR_ADDED_A_NEW_POST') : JText::_('RSB_COMMUNITY_ACTOR_HAS_UPDATED_HIS_BLOG_POST'), $post_link, $blog_link);
				$act->content = RSBlogHelper::shorten(strip_tags($row->introtext), 100);
				$act->app     = 'rsblog';
				$act->cid     = $row->id;

				CFactory::load('libraries', 'activities');
				CActivityStream::add($act);
			}
			
			return $row->id;
		}
		
		JError::raiseWarning(500, $row->getError());
		return false;
	}
	
	function share($cid,$admin = true)
	{
		//get the application
		$application =& JFactory::getApplication();
		
		//get configuration
		$config = RSBlogHelper::getConfig();
		
		//get trackback variable
		$trackback = JRequest::getVar('sendtrackbacks',0);
		//get pingback variable
		$pingback = JRequest::getVar('sendpingbacks',0);
		//get twitter variable
		$twitter = JRequest::getVar('sendtwitter',0);
		//get facebook variable
		$facebook = JRequest::getVar('sendfacebook',0);
		
		//add trackbacks and pingbacks
		if ($trackback || $pingback || ($twitter && $config->twitter_enable) || ($facebook && $config->facebook_enable) )
		{
			$params = array();
			
			if ($trackback)
			{
				RSBlogHelper::addTrackbacks($cid);
				$params[] = '&trackback=1';
			}
			
			if ($pingback)
			{
				RSBlogHelper::addPingbacks($cid);
				$params[] = '&pingback=1';
			}
			
			if ($twitter && $config->twitter_enable)
			{
				RSBlogHelper::addTwitter($cid);
				$params[] = '&twitter=1';
			}
			
			if ($facebook && $config->facebook_enable)
			{
				RSBlogHelper::addFacebook($cid);
				$params[] = '&facebook=1';
			}
			
			if (!empty($params))
				$params = implode('',$params);
			
			//redirect to trackback page
			if ($admin)
				$application->redirect('index.php?option=com_rsblog&view=posts&layout=trackbacks'.$params.'&cid='.$cid);
			else $application->redirect(RSBlogHelper::route('index.php?option=com_rsblog&layout=sharing'.$params.'&cid='.$cid,false));
		}
		
		return true;
	}
	
	//translate dates
	function translate($string)
	{
		$months_english = array('January','February','March','April','May','June','July','August','September','October','November','December');
		$months_translate = array(JText::_('RSB_JANUARY'),JText::_('RSB_FEBRUARY'),JText::_('RSB_MARCH'),JText::_('RSB_APRIL'),JText::_('RSB_MAY'),JText::_('RSB_JUNE'),JText::_('RSB_JULY'),JText::_('RSB_AUGUST'),JText::_('RSB_SEPTEMBER'),JText::_('RSB_OCTOBER'),JText::_('RSB_NOVEMBER'),JText::_('RSB_DECEMBER'));
		$months_english_short = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$months_translate_short = array(JText::_('RSB_JANUARY_SHORT'),JText::_('RSB_FEBRUARY_SHORT'),JText::_('RSB_MARCH_SHORT'),JText::_('RSB_APRIL_SHORT'),JText::_('RSB_MAY_SHORT'),JText::_('RSB_JUNE_SHORT'),JText::_('RSB_JULY_SHORT'),JText::_('RSB_AUGUST_SHORT'),JText::_('RSB_SEPTEMBER_SHORT'),JText::_('RSB_OCTOBER_SHORT'),JText::_('RSB_NOVEMBER_SHORT'),JText::_('RSB_DECEMBER_SHORT'));
		$days_english = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
		$days_translate = array(JText::_('RSB_MONDAY'),JText::_('RSB_TUESDAY'),JText::_('RSB_WEDNESDAY'),JText::_('RSB_THURSDAY'),JText::_('RSB_FRIDAY'),JText::_('RSB_SATURDAY'),JText::_('RSB_SUNDAY'));
		$days_english_short = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		$days_translate_short = array(JText::_('RSB_MONDAY_SHORT'),JText::_('RSB_TUESDAY_SHORT'),JText::_('RSB_WEDNESDAY_SHORT'),JText::_('RSB_THURSDAY_SHORT'),JText::_('RSB_FRIDAY_SHORT'),JText::_('RSB_SATURDAY_SHORT'),JText::_('RSB_SUNDAY_SHORT'));
		
		$string = str_replace($months_english,$months_translate,$string);
		$string = str_replace($months_english_short,$months_translate_short,$string);
		$string = str_replace($days_english,$days_translate,$string);
		$string = str_replace($days_english_short,$days_translate_short,$string);
		
		return $string;
	}
	
	function getUrl()
	{
		$url = 'index.php?option=com_rsblog';
		if (!RSBlogHelper::is16()) return $url;
		
		jimport('joomla.plugin.helper');
		$db =& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$conf =& JFactory::getConfig();
		
		$default = $lang->getDefault();
		
		$db->setQuery("SELECT `sef` FROM #__languages WEHRE `lang_code` = '".$default."' ");
		$lang_sef = $db->loadResult();
		
		if (empty($lang_sef)) $lang_sef = 'en';
		
		if (JPluginHelper::isEnabled('system', 'languagefilter'))
			$url = $lang_sef.'/'.$url;
		
		return $url;
	}
	
	
}