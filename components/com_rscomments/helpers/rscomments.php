<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'models'.DS.'comments.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'emoticons.php');

class RSCommentsHelper
{
	function readConfig()
	{
		$mainframe =& JFactory::getApplication();
		$rscomments_config = new stdClass();
		
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__rscomments_config`");
		$config = $db->loadObjectList();
		if (!empty($config))
			foreach ($config as $config_item)
				$rscomments_config->{$config_item->name} = $config_item->value;
		$mainframe->setUserState('rscomments_config', $rscomments_config);
	}
	
	function getConfig($name = null)
	{
		$mainframe =& JFactory::getApplication();
		$config = $mainframe->getUserState('rscomments_config');
		if ($name != null)
		{
			if (isset($config->$name))
				return $config->$name;
			else
				return false;
		}
		else
			return $config;
	}
	
	function is16()
	{
		$jversion = new JVersion();
		$current_version =  $jversion->getShortVersion();
		return (version_compare('1.6.0', $current_version) <= 0);
	}
	
	function clean(&$content)
	{
		$pattern = '/{rscomments\s+(on|off)}/is';
		
		if (isset($content->text)) $content->text = preg_replace($pattern, '', $content->text);
		if (isset($content->introtext)) $content->introtext = preg_replace($pattern, '', $content->introtext);
		if (isset($content->fulltext)) $content->fulltext = preg_replace($pattern, '', $content->fulltext);
	}
	
	function rscOn($content)
	{
		$return = false;
		$pattern = '/{rscomments\s+on}/is';
		if(isset($content->text) && preg_match($pattern, $content->text)) $return  = true;
		
		return $return;
	}
	
	function rscOff($content)
	{
		$return = false;
		$pattern = '/{rscomments\s+off}/is';
		if(isset($content->text) && preg_match($pattern, $content->text)) $return  = true;
		
		return $return;
	}
	
	function getTemplate()
	{
		RSCommentsHelper::readConfig();
		return RSCommentsHelper::getConfig('template');
	}
	
	function getCommentsNumber($id,$article = false,$option='')
	{
		$db =& JFactory::getDBO();
		$condition = ($article) ? " AND `option` = 'com_content' " : " AND `option` = '".$db->getEscaped($option)."' " ;
		
		$db->setQuery("SELECT COUNT(`IdComment`) FROM `#__rscomments_comments` WHERE `id` = ".(int) $id." ".$condition." AND published = 1 ");
		return $db->loadResult();
	}
	
	function show($option,$id,$template,$container = null,$override=false)
	{
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$doc =& JFactory::getDocument();
		$u =& JFactory::getURI();
		$with_pagination = JRequest::getInt('pagination',0);
		RSCommentsHelper::readConfig();
		
		if($option == 'com_content')
		{
			$categories = RSCommentsHelper::getConfig('categories');
			
			if (!RSCommentsHelper::is16())
			{
				$sections = RSCommentsHelper::getConfig('sections');
				if(trim($sections) && !$override)
				{
					$sections = explode(',',$sections);
					$db->setQuery("SELECT `sectionid` FROM `#__content` WHERE `id` = ".$id);
					$sectionid = $db->loadResult();
					if(in_array($sectionid,$sections)) return;
				}
			}
			
			if(trim($categories) && !$override)
			{
				$categories = explode(',',$categories);
				$db->setQuery("SELECT `catid` FROM #__content WHERE `id` = '".$id."'");
				$categoryId = $db->loadResult();
				
				if(in_array($categoryId,$categories)) return;
			}
		}
		
		//get the root
		$root = $u->toString(array('scheme','host'));
		
		//get the config
		$config = RSCommentsHelper::getConfig();
		
		$object = '';
		
		if($container == null)
			$object .= '<div id="rscomments_big_container">';
		else $object .= '';
		
		//load the comment class		
		$commentsClass = new RSCommentsModelComments($id,$option,$config->nr_comments);
		
		//load the pagination
		$pagination = $commentsClass->getPagination();
		
		//load the comments
		$comments = $commentsClass->getComments();
		
		//load permissions
		$permissions = $commentsClass->getPermissions();
		
		$subscribeLink = $user->id > 0 ? '<span id="rsc_subscr"><a href="javascript:void(0);" onclick="rsc_subscribe(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$id.'\',\''.$option.'\')"><img src="'.JURI::root().'components/com_rscomments/assets/images/subscribe.png" alt="'.JText::_('RSC_SUBSCRIBE').'" /> '.JText::_('RSC_SUBSCRIBE').'</a></span>' : '';
		$unsubscribeLink = $user->id > 0 ? '<span id="rsc_subscr"><a href="javascript:void(0);" onclick="rsc_unsubscribe(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$id.'\',\''.$option.'\')"><img src="'.JURI::root().'components/com_rscomments/assets/images/subscribe.png" alt="'.JText::_('RSC_UNSUBSCRIBE').'" /> '.JText::_('RSC_UNSUBSCRIBE').'</a></span>' : '';
		
		$isSubscribed = $commentsClass->getSubscriber($id,$option);
		
		if ($config->enable_subscription)
			$SUlink = $isSubscribed ? $unsubscribeLink : $subscribeLink;
		else $SUlink = '';
		
		$rssLink = $config->enable_rss == 1 ? ' <a href="index.php?option=com_rscomments&amp;format=feed&amp;type=rss&amp;opt='.$option.'&amp;id='.$id.'"><img src="'.JURI::root().'components/com_rscomments/assets/images/rss.png" alt="'.JText::_('RSC_RSS').'" /> '.JText::_('RSC_RSS').'</a>' : '';
		
		$hr = !empty($comments) ? '<div id="rsc_loading" style="text-align:center;display:none;"><img src="'.JURI::root().'components/com_rscomments/assets/images/loader.gif" alt="loading..."/></div><div class="rsc_comment_options">'.$SUlink.$rssLink.'</div><br/><br/>' : '';
		$object .= $hr;
		
		//check for the template
		if(!file_exists(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'designs'.DS.$template.DS.$template.'.html') && !file_exists(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'designs'.DS.$template.DS.$template.'.css')) return;
		
		//set the template and add the stylesheet
		$doc->addStyleSheet(JURI::root().'components/com_rscomments/designs/'.$template.'/'.$template.'.css');
		
		if (!empty($comments))
		foreach($comments as $comment)
		{
			$object .= '<div id="rscomment'.$comment->IdComment.'">'; 
			$layout = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'designs'.DS.$template.DS.$template.'.html');
			
			$newu = JFactory::getUser($comment->uid);
			$avatar = RSCommentsHelper::getAvatar($newu->id,$comment->email);
			
			
			//set the name
			switch($config->author_name)
			{
				case 'username':
					$name = (isset($permissions['show_emails']) && $permissions['show_emails']) ? '<a href="mailto:'.$newu->email.'">'.$newu->username.'</a>' : $newu->username;
					$sname = $newu->username;
				break;
				case 'name':
					$name = (isset($permissions['show_emails']) && $permissions['show_emails']) ? '<a href="mailto:'.$newu->email.'">'.$newu->name.'</a>' : $newu->name;
					$sname = $newu->name;
				break;
				case 'cb':
					$db->setQuery("SELECT CONCAT_WS( ' ', firstname,lastname ) AS `name` FROM `jos_comprofiler` WHERE `user_id` = '".$newu->id."'");
					$cb = $db->loadResult();
					$cbname = !empty($cb) ? $cb : $newu->name;
					$name = (isset($permissions['show_emails']) && $permissions['show_emails']) ? '<a href="mailto:'.$newu->email.'">'.$cbname.'</a>' : $cbname;
					$sname = $cbname;
				break;
			}
			
			if($comment->uid == 0)
			{
				$name  = (isset($permissions['show_emails']) && $permissions['show_emails']) ? '<a href="mailto:'.$comment->email.'">'.$comment->name.'</a>' : $comment->name;
				$sname = $comment->name; 
			}
			
			//set the website 
			$website = ($config->enable_website_field == 1) ? '<a href="'.$comment->website.'" target="_blank" title="'.JText::_('RSC_WEBSITE').'"><img src="'.JURI::root().'components/com_rscomments/assets/images/web.png" alt="'.$comment->website.'" /></a>' : '';
			
			//set the subject
			$subject = ($config->enable_title_field == 1) ? '<span id="rscsubject'.$comment->IdComment.'">'.$comment->subject.'</span>' : '';
			
			//set the date
			$date = date($config->date_format,$comment->date);
			
			//parse the comment
			$comment->comment = RSCommentsHelper::parseComment($comment->comment,$permissions);
			
			
			//set the comment
			$commenttext =  '<span id="c'.$comment->IdComment.'">'.$comment->comment.'</span>';
			
			if(isset($permissions['new_comments']) && $permissions['new_comments'])
				$commenttext .=  '<div class="rsc_rq"><a href="javascript:void(0);" onclick="rsc_quote(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$sname.'\',\''.$comment->IdComment.'\');">'.JText::_('RSC_COMMENT_QUOTE').'</a></div>';
			
			$replace = array('{AuthorName}','{comment}','{avatar}','{AuthorWebsite}','{subject}','{date}');
			$with = array('<span id="rscname'.$comment->IdComment.'">'.$name.'</span>',$commenttext,$avatar,$website,$subject,$date);
			
			
			//set the ip button
			if(isset($permissions['view_ip']) && $permissions['view_ip']) 
			{ 
				$replace[] = '{AuthorIP}'; 
				$with[] = '<a href="http://www.db.ripe.net/whois?searchtext='.$comment->ip.'" target="_blank" title="'.$comment->ip.'::" class="hasTip"><img src="'.JURI::root().'components/com_rscomments/assets/images/info.png" alt="" /></a>'; 
			} else { $replace[] = '{AuthorIP}'; $with[] = ''; }
			
			//set the edit button
			if(((isset($permissions['edit_own_comment']) && $permissions['edit_own_comment']) && $user->id == $comment->uid) || (isset($permissions['edit_comments']) && $permissions['edit_comments'])) 
			{ 
				$replace[] = '{EditComment}'; 
				$with[] = '<a href="javascript:void(0);" onclick="rsc_edit(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\');" title="'.JText::_('RSC_EDIT_COMMENT').'"><img src="'.JURI::root().'components/com_rscomments/assets/images/edit.png" alt="'.JText::_('RSC_EDIT_COMMENT').'" /></a>'; 
			} else { $replace[] = '{EditComment}'; $with[] = ''; }
			
			//set the delete button
			if( ( (isset($permissions['delete_own_comment']) && $permissions['delete_own_comment']) && $user->id == $comment->uid ) || (isset($permissions['delete_comments']) && $permissions['delete_comments'] )) 
			{ 
				$replace[] = '{DeleteComment}'; 
				$with[] = '<a href="javascript:void(0);" onclick="if(confirm(\''.JText::_('RSC_DELETE_COMMENT_CONFIRM',true).'\')) rsc_delete(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\'); else return false;" title="'.JText::_('RSC_DELETE_COMMENT').'"><img src="'.JURI::root().'components/com_rscomments/assets/images/trash.png" alt="'.JText::_('RSC_DELETE_COMMENT').'" /></a>'; 
			} else { $replace[] = '{DeleteComment}'; $with[] = ''; }
			
			//set the publish/unpublish button
			if(isset($permissions['publish_comments']) && $permissions['publish_comments'])
			{ 
				$publish = ($comment->published == 1) ? 'lighton' : 'lightoff'; 
				$function = ($comment->published == 1) ? 'rsc_unpublish(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\')' : 'rsc_publish(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\')'; 
				$message = ($comment->published == 1) ? JText::_('RSC_UNPUBLISH') : JText::_('RSC_PUBLISH');
				$replace[] = '{PublishComment}'; 
				$with[] = '<span id="rsc_publish'.$comment->IdComment.'"><a href="javascript:void(0);" onclick="'.$function.'" title="'.$message.'"><img src="'.JURI::root().'components/com_rscomments/assets/images/'.$publish.'.png" alt="'.$message.'" /></a></span>'; 
			} else { $replace[] = '{PublishComment}'; $with[] = ''; }
			
			//set the vote buttons
			$enablevoting = ($config->enable_votes == 1) ? true : false; 
			
			if($enablevoting)
			{
				$positive = '<a href="javascript:void(0);" onclick="rsc_pos(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\');" title="'.JText::_('RSC_GOOD_COMMENT').'"><img src="'.JURI::root().'components/com_rscomments/assets/images/voteyes.png" alt="'.JText::_('RSC_YES').'" /></a> ';
				$negative = '<a href="javascript:void(0);" onclick="rsc_neg(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.$comment->IdComment.'\');" title="'.JText::_('RSC_BAD_COMMENT').'"><img src="'.JURI::root().'components/com_rscomments/assets/images/voteno.png" alt="'.JText::_('RSC_NO').'" /></a>';
				
				$db->setQuery("SELECT COUNT(`IdVote`) FROM `#__rscomments_votes` WHERE `IdComment` = '".$comment->IdComment."' AND `value` = 'positive' ");
				$pos = $db->loadResult();
				$db->setQuery("SELECT COUNT(`IdVote`) FROM `#__rscomments_votes` WHERE `IdComment` = '".$comment->IdComment."' AND `value` = 'negative' ");
				$neg = $db->loadResult();
				
				
				if(isset($permissions['vote_comments']) && $permissions['vote_comments'])
				{
					$replace[] = '{vote}';
					if ($user->get('guest'))
						$query = "SELECT `IdVote` FROM #__rscomments_votes WHERE `IdComment`= '".$comment->IdComment."' AND `ip`='".$_SERVER['REMOTE_ADDR']."'";
					else
						$query = "SELECT `IdVote` FROM #__rscomments_votes WHERE `IdComment`= '".$comment->IdComment."' AND (`ip`='".$_SERVER['REMOTE_ADDR']."' OR `uid`='".$user->id."')";
					
					$db->setQuery($query);
					$voted = $db->loadResult();
					
					if(empty($voted))
						$with[] = '<span id="rsc_voting'.$comment->IdComment.'">'.$positive.$negative.'</span>';
					else 					
						$with[] = $pos - $neg;
				} else { $replace[] = '{vote}'; $with[] = $pos - $neg; }
			} else { $replace[] = '{vote}'; $with[] = ''; }
			
			if ($config->enable_upload)
			{
				$replace[] = '{attachement}';
				if (!empty($comment->file))
					$with[] = '<a href="'.JRoute::_('index.php?option=com_rscomments&task=download&cid='.$comment->IdComment,false).'"><img src="'.JURI::root().'components/com_rscomments/assets/images/attachement.png" alt="'.JText::_('RSC_ATTACHEMENT').'" /></a>';
				else 
					$with[] = '';
			} else {
				$replace[] = '{attachement}';
				$with[] = '';
			}
			
			
			$object .= str_replace($replace,$with,$layout);
			$object .= '</div>';	
		}
		
		if($container == null) $object .= '</div>';
		
		if ($with_pagination == 0)
		{
			$task		= JRequest::getCmd('task');
			$joption	= JRequest::getCmd('option');
			
			
			if ($joption == 'com_rscomments' && $task == 'pagination')
				$object .= '{sep}';
			else
				$object .= '<div id="rsc_global_pagination" class="rsc_global_paginations">';
			$object .= '<div style="text-align:center;">'.RSCommentsHelper::ajaxpagination($pagination->getPagesLinks(),$option,$id,$template,$pagination,$override).'</div>';
			$object .= '<br/>';
			$object .= '<div style="text-align:center;">'.$pagination->getPagesCounter().'</div>';
			if ($joption == 'com_rscomments' && $task == 'pagination')
				$object .= '';
			else
				$object .= '</div>';
		}
	
		return $object;
	}
	
	function showForm($option,$id,$override=false)
	{
		$db =& JFactory::getDBO();
		$doc =& JFactory::getDocument();
		$u =& JFactory::getURI();
		$user =& JFactory::getUser();
		RSCommentsHelper::readConfig();
		
		if($option == 'com_content')
		{
			$categories = RSCommentsHelper::getConfig('categories');
			$sections = RSCommentsHelper::getConfig('sections');
			
			if (!RSCommentsHelper::is16())
			{
				if(trim($sections) && !$override)
				{
					$sections = explode(',',$sections);
					$db->setQuery("SELECT `sectionid` FROM `#__content` WHERE `id` = ".$id);
					$sectionid = $db->loadResult();

					if(in_array($sectionid,$sections)) return;
				}
			}
			
			if(trim($categories) && !$override)
			{
				$categories = explode(',',$categories);
				$db->setQuery("SELECT `catid` FROM #__content WHERE `id` = '".$id."'");
				$categoryId = $db->loadResult();
				
				if(in_array($categoryId,$categories)) return;
			}
		}
		
		//get the root
		$root = $u->toString(array('scheme','host'));
		
		//get the config
		$config = RSCommentsHelper::getConfig();
		
		//load the comment class
		$commentsClass = new RSCommentsModelComments($id,$option);
		
		//load permissions
		$permissions = $commentsClass->getPermissions();
		
		if(empty($permissions['new_comments'])) 
		{
			$msg = empty($config->comments_denied) ? $config->comments_denied : ''.$config->comments_denied;
			return $msg;
		}
		
		
		if ($config->form_accordion)
		$doc->addScriptDeclaration("\n//<![CDATA[\n\t\twindow.addEvent('domready', function() {
				var myAccordion = new Accordion($('rsc_accordion_container'), 'span.rsc_toggler', 'div.rscomments_show_form', {
				duration: 300,
				opacity: true,
				alwaysHide: true,
				onActive: function(toggler, element){
					toggler.innerHTML = '".JText::_('RSC_HIDE_FORM',true)." <img style=\"vertical-align:middle;\" src=\"".JURI::root()."components/com_rscomments/assets/images/up.png\" alt=\"\" height=\"16\" width=\"16\" />';
					toggler.setStyle('color', '#000000');
					toggler.setStyle('font-weight', 'bold');
				},
				onBackground: function(toggler, element){
					toggler.innerHTML = '".JText::_('RSC_SHOW_FORM',true)." <img style=\"vertical-align:middle;\" src=\"".JURI::root()."components/com_rscomments/assets/images/down.png\" alt=\"\" height=\"16\" width=\"16\" />';
					toggler.setStyle('color', '#000000');
					toggler.setStyle('font-weight', 'bold');
				}
			});
		});\n//]]>\n");
		
		
		$disable = ($user->id != 0) ? 'disabled="disabled"' : '';
		
		$html = '<div id="rsc_loading_form" style="text-align:center;display:none;"><img src="'.JURI::root().'components/com_rscomments/assets/images/loader.gif" alt="loading..."/></div>';	
		$html .= '<form name="rscommentsForm" action="javascript:void(0)" method="post">';
		
		if ($config->form_accordion)
		{
			$html .= '<div id="rsc_accordion_container">';
			$html .= '<span class="rsc_toggler">'.JText::_('RSC_HIDE_FORM').' <img id="rsc_accordion_direction" style="vertical-align:middle;" src="'.JURI::root().'components/com_rscomments/assets/images/up.png" alt="" height="16" width="16" /></span>';
			$html .= '<span class="rsc_clear"></span>';
			$html .= '<div class="rscomments_show_form">';
		}
		
		
		$html .= '<div class="rscomments_form_container">';
		$html .= '<div class="rscomments_small_container">';
		$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_NAME_DESC').'" for="rsc_name"><span>'.JText::_('RSC_COMMENT_NAME').'</span></label> <input '.$disable.' type="text" class="rsc_input" id="rsc_name" name="name" value="'.$user->name.'" size="45" /></p>';
		$html .= '<span class="rsc_clear"></span>';
		
		$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_EMAIL_DESC').'" for="rsc_email"><span>'.JText::_('RSC_COMMENT_EMAIL').'</span></label> <input '.$disable.' type="text" class="rsc_input" id="rsc_email" name="email" value="'.$user->email.'" size="45" /></p>';
		$html .= '<span class="rsc_clear"></span>';
		
		if($config->enable_title_field == 1)
		{
			$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_SUBJECT_DESC').'" for="rsc_subject"><span>'.JText::_('RSC_COMMENT_SUBJECT').'</span></label> <input type="text" class="rsc_input" id="rsc_subject" name="subject" value="" size="45" /></p>';
			$html .= '<span class="rsc_clear"></span>';
		}
		
		if($config->enable_website_field == 1)
		{
			$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_WEBSITE_DESC').'" for="rsc_website"><span>'.JText::_('RSC_COMMENT_WEBSITE').'</span></label> <input type="text" class="rsc_input" id="rsc_website" name="website" value="" size="45" /></p>';
			$html .= '<span class="rsc_clear"></span>';
		}
		
		$html .= RSCommentsHelper::showIcons($permissions);
		
		$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_COMMENT_DESC').'" for="rsc_comment"><span>'.JText::_('RSC_COMMENT_COMMENT').'</span></label> <textarea id="rsc_comment" name="comment" cols="50" rows="10"  onkeydown="rsc_comment_cnt(document.rscommentsForm.comment,\'commentlen\','.(int) $config->max_comm_len.');" onkeyup="rsc_comment_cnt(document.rscommentsForm.comment,\'commentlen\','.(int) $config->max_comm_len.');"></textarea>';
		$html .= ($config->show_counter == 1) ? '<span id="commentlen">'.(int) $config->max_comm_len.'</span> '.JText::_('RSC_CHARS_LEFT') : '<span id="commentlen" style="display:none;">'.$config->max_comm_len.'</span>';
		$html .= '</p>';
		$html .= '<span class="rsc_clear"></span>';
		
		if ($config->terms)
		{
			JHTML::_('behavior.modal');
			$html .= '<p><label class="rsc_label">&nbsp;</label> <input type="checkbox" id="rsc_terms" name="rsc_terms" value="1" /> <label for="rsc_terms"><a class="modal" rel="{handler : \'iframe\'}" href="'.JRoute::_('index.php?option=com_rscomments&task=terms').'">'.JText::_('RSC_TERMS').'</a></label></p>';
			$html .= '<span class="rsc_clear"></span>';
		}
		
		if ($config->enable_upload)
		{
			$html .= '<p><label class="rsc_label">&nbsp;</label>';
			$html .= '<iframe src="'.JRoute::_('index.php?option=com_rscomments&task=upload').'" name="rsc_frame" id="rsc_frame" frameborder="0" scrolling="no" width="50%" height="40"></iframe>';
			$html .= '</p>';
			$html .= '<span class="rsc_clear"></span>';
		}
		
		if(isset($permissions['captcha']) && $permissions['captcha'])
		{
			$html .= '<p><label class="hasTip rsc_label" title="'.JText::_('RSC_COMMENT_CAPTCHA_DESC').'" for="submit_captcha"><span>'.JText::_('RSC_COMMENT_CAPTCHA').'</span></label>';
			if($config->captcha == 0)
				$html .= '<img src="'.JRoute::_('index.php?option=com_rscomments&task=captcha').'" id="submit_captcha_image" alt="Antispam" height="80" />
						<span class="hasTip" title="'.JText::_('RSC_REFRESH_CAPTCHA_DESC').'">
							<a style="border-style: none" href="javascript:void(0)" onclick="return rsc_refresh_captcha(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments&task=captcha').'\');">
								<img src="'.JURI::root().'/components/com_rscomments/assets/images/refresh.gif" alt="'.JText::_('RSC_REFRESH_CAPTCHA').'" border="0" onclick="this.blur()" align="top" />
							</a>
							'.($config->captcha_cases ? JText::_('RSC_CAPTCHA_CASE_SENSITIVE') : JText::_('RSC_CAPTCHA_CASE_INSENSITIVE')).'
						</span></p>
						<p><label class="rsc_label" for="submit_captcha">&nbsp;</label><input type="text" name="captcha" id="submit_captcha" size="40" value="" class="inputbox" /></p>';
			else 
			{
				require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'recaptcha'.DS.'recaptchalib.php');
				$html .= JReCAPTCHA::getHTML().'<span id="rsc_recaptcha">&nbsp;</span>';
			}
			$html .= '<span class="rsc_clear"></span>';
		}
		
		$upload = $config->enable_upload ? 1 : 0;
		
		$html .= '<p><label class="rsc_label">&nbsp;</label><button type="button" class="button" onclick="rsc_save(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\',\''.JRoute::_('index.php?option=com_rscomments&task=captcha').'\',\''.$upload.'\');">'.JText::_('RSC_SEND').'</button> <button type="reset" class="button">'.JText::_('RSC_RESET').'</button></p>';
		$html .= '</div>';
		$html .= '</div>';
		
		if ($config->form_accordion)
		{
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<span class="rsc_clear"></span>';
		}
		
		$html .= '<input type="hidden" name="obj_option" value="'.$option.'" />';
		$html .= '<input type="hidden" name="url" value="'.RSCommentsHelper::getUrl().'" />';
		$html .= '<input type="hidden" name="obj_id" value="'.$id.'" />';
		$html .= '<input type="hidden" name="controller" value="comments" />';
		$html .= '<input type="hidden" name="IdComment" value="" />';
		$html .= '<input type="hidden" name="task" value="save" />';
		$html .= '<input type="hidden" name="override" value="'.(int) $override.'" />';
		$html .= '</form>';
		if($config->enable_smiles == 1)
			$html .= '<script type="text/javascript">document.onclick=rsc_check;</script>';
		
		return $html;
	}
	
	function showRSComments($option,$id,$template=null,$container=null, $override=false)
	{
		$doc 		=& JFactory::getDocument();
		$lang		=& JFactory::getLanguage();
		
		//load the js and css code
		$doc->addScript(JURI::root().'components/com_rscomments/assets/js/rscomments.js');
		$doc->addScript(JURI::root().'components/com_rscomments/assets/js/bbcode.js');
		$doc->addStyleSheet(JURI::root().'components/com_rscomments/assets/css/style.css');
		
		//load language file
		$lang->load('com_rscomments');
		
		$template = is_null($template) ? 'default' : $template;
		
		$return = RSCommentsHelper::show($option,$id,$template,$container,$override);
		$return .= RSCommentsHelper::showForm($option,$id,$override);
		
		return $return;
	}
	
	function getUrl()
	{
		$u    =& JURI::getInstance('SERVER');
		$base =  JURI::base();
		$url = $u->getScheme().'://'.$u->getHost().JRequest::getURI();
		$url = str_replace($base,'',$url);
		$url = base64_encode($url);
		
		return $url;
	}
	
	function showIcons($permissions)
	{
		$config = RSCommentsHelper::getConfig();
		
		$html = '';
		
		if(($config->enable_bbcode == 1 && (isset($permissions['bbcode']) && $permissions['bbcode'])) || $config->enable_smiles == 1)
		{
			$html .= '<p>';
			$html .= '<label class="rsc_label">&nbsp;</label>';
		}
		
		if($config->enable_bbcode == 1 && (isset($permissions['bbcode']) && $permissions['bbcode']))
			$html .= RSCommentsHelper::createBBs($permissions);
		
		if($config->enable_smiles == 1) 
			$html .= ' <img id="rsc_emoti_on" src="'.JURI::root().'components/com_rscomments/assets/images/smiley.gif" onclick="rsc_show_emoticons(this);" alt="Smileys" />';
		
		if(($config->enable_bbcode == 1 && (isset($permissions['bbcode']) && $permissions['bbcode'])) || $config->enable_smiles == 1)
			$html .= '</p>';
		
		if($config->enable_smiles == 1)
		{
			$emoticons = RSCommentsEmoticons::createEmoticons();
			$html .= ' <p><label class="rsc_label">&nbsp;</label>';
			$html .= ' <span id="rsc_emoticons" class="rsc_emoticons">'.$emoticons.'</span></p>';
			$html .= '<span class="rsc_clear"></span>';
		} 
		
		return $html;
	}
	
	function parseComment($comment,$permissions)
	{
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
		
		$patterns = array();
		$replacements = array();

		// B
		$patterns[] = '/\[b\](.*?)\[\/b\]/i';
		$replacements[] = (isset($permissions['bb_bold']) && $permissions['bb_bold']) ? '<b>\\1</b>' : '\\1';

		// I
		$patterns[] = '/\[i\](.*?)\[\/i\]/i';
		$replacements[] = (isset($permissions['bb_italic']) && $permissions['bb_italic']) ? '<i>\\1</i>' : '\\1';

		// U
		$patterns[] = '/\[u\](.*?)\[\/u\]/i';
		$replacements[] = (isset($permissions['bb_underline']) && $permissions['bb_underline']) ? '<u>\\1</u>' : '\\1';

		// S
		$patterns[] = '/\[s\](.*?)\[\/s\]/i';
		$replacements[] = (isset($permissions['bb_stroke']) && $permissions['bb_stroke']) ? '<strike>\\1</strike>' : '\\1';

		// URL
		$nofollow = $config->no_follow ? 'rel="nofollow"' : '';
		$patterns[] = '/(\[url=)(.+)(\])(.+)(\[\/url\])/i';
		$replacements[] = (isset($permissions['bb_url']) && $permissions['bb_url']) ? '<a href="\\2" '.$nofollow.'>\\4</a>' : '\\2';
		
		$patterns[] = '/\[url\]([ a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\']*)\[\/url\]/i';
		$replacements[] = (isset($permissions['bb_url']) && $permissions['bb_url']) ? '<a href="\\1" '.$nofollow.'>\\1</a>' : '\\1';
		
		// IMG
		$patterns[] = '#\[img\](http:\/\/)?([^\s\<\>\(\)\"\']*?)\[\/img\]#i';
		$replacements[] = (isset($permissions['bb_image']) && $permissions['bb_image']) ? '<img src="http://\\2" alt="" border="0" />' : '\\2';
		$patterns[] = '#\[img\](.*?)([^\s<>()\"\']*?)(.*?)\[\/img\]#i';
		$replacements[] = '[img:error]';

		// CODE
		$patterns[] = '#\[code\](.*?)\[\/code\]#ism';
		$replacements[] = (isset($permissions['bb_code']) && $permissions['bb_code']) ? '<span class="rsc_code">'.JText::_('RSC_CODE').' : <pre>\\1</pre></span>' : '\\1';

		//YOUTUBE
		$patterns[] = '#\[youtube\]http\://www\.youtube\.com/watch\?v\=(.+?)\[\/youtube\]#';
		$replacements[] = (isset($permissions['bb_videos']) && $permissions['bb_videos']) ? '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/\\1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/\\1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>' : 'http://www.youtube.com/v/\\1';
		
		//GOOGLE VIDEOS
		$patterns[] = '/\[google\](.+?)\[\/google\]/';
		$patterns[] = '#\[google\]http\://video\.google\.com/videoplay\?docid\=([A-Za-z0-9-_]+)\[\/google\]#';
		$replacements[] = (isset($permissions['bb_videos']) && $permissions['bb_videos']) ? '<object style="width:400px; height:326px;">
			<param name="movie" value=http://video.google.com/googleplayer.swf?docId=\\1"></param>
			<embed src="http://video.google.com/googleplayer.swf?docId=\\1" wmode="transparent" style="width:400px; height:326px;" 
			id="VideoPlayback" type="application/x-shockwave-flash" flashvars=""></embed>' : 'http://video.google.com/googleplayer.swf?docId=\\1';
		
		
		$comment = preg_replace($patterns, $replacements, htmlentities($comment,ENT_COMPAT,'UTF-8'));
		
		// QUOTE
		$quotePattern = '#\[quote\s?name=\"([^\"\'\<\>\(\)]+)+\"\](<br\s?\/?\>)*(.*?)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '<span class="rsc_quote">'.JText::_('RSC_QUOTE_USER').' "\\1" : <blockquote>\\3</blockquote> </span>';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}
		$quotePattern = '#\[quote[^\]]*?\](<br\s?\/?\>)*([^\[]+)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '<span class="rsc_quote">'.JText::_('RSC_QUOTE_SINGLE').' : <blockquote>\\2</blockquote> </span>';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}

		// LIST
		$matches = array();
		$matchCount = preg_match_all('#\[list\](<br\s?\/?\>)*(.*?)(<br\s?\/?\>)*\[\/list\]#is', $comment, $matches);
		for ($i = 0; $i < $matchCount; $i++) {
			$textBefore = preg_quote($matches[2][$i]);
			$textAfter = preg_replace('#(<br\s?\/?\>)*\[\*\](<br\s?\/?\>)*#is', "</li>\n<li>", $matches[2][$i]);
			$textAfter = preg_replace("#^</?li>#", "", $textAfter);
			$textAfter = str_replace("\n</li>", "</li>", $textAfter."</li>");
			$comment = preg_replace('#\[list\](<br\s?\/?\>)*' . $textBefore . '(<br\s?\/?\>)*\[/list\]#is', "\n<ul>$textAfter\n</ul>\n", $comment);
		}
		$matches = array();
		$matchCount = preg_match_all('#\[list=(a|A|i|I|1)\](<br\s?\/?\>)*(.*?)(<br\s?\/?\>)*\[\/list\]#is', $comment, $matches);
		for ($i = 0; $i < $matchCount; $i++) {
			$textBefore = preg_quote($matches[3][$i]);
			$textAfter = preg_replace('#(<br\s?\/?\>)*\[\*\](<br\s?\/?\>)*#is', "</li>\n<li>", $matches[3][$i]);
			$textAfter = preg_replace("#^</?li>#", '', $textAfter);
			$textAfter = str_replace("\n</li>", "</li>", $textAfter."</li>");
			$comment = preg_replace('#\[list=(a|A|i|I|1)\](<br\s?\/?\>)*' . $textBefore . '(<br\s?\/?\>)*\[/list\]#is', "\n<ol type=\\1>\n$textAfter\n</ol>\n", $comment);
		}	

		$comment = preg_replace('#\[\/?(b|i|u|s|url|img|list|quote|code)\]#', '', $comment);
		if($config->enable_smiles == 1) $comment = RSCommentsEmoticons::cleanText($comment);
		$comment = RSCommentsHelper::breakwords($comment);
		$comment = RSCommentsHelper::newlinetobr($comment);
		
		return $comment;
	}
	
	function cleanComment($comment)
	{
		
		$patterns = array();
		$replacements = array();
		
		$patterns[] = '/\[b\](.*?)\[\/b\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[i\](.*?)\[\/i\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[u\](.*?)\[\/u\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[s\](.*?)\[\/s\]/i';
		$replacements[] = '\\1';

		$patterns[] = '/\[url\]([ a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\']*)\[\/url\]/i';
		$replacements[] = '\\1';

		$patterns[] = '#\[img\](http:\/\/)?([^\s\<\>\(\)\"\']*?)\[\/img\]#i';
		$replacements[] = '\\2';

		$patterns[] = '#\[code\](.*?)\[\/code\]#ism';
		$replacements[] = '\\1';

		//YOUTUBE
		$patterns[] = '/\[youtube\](.+?)\[\/youtube\]/';
		$replacements[] = '\\1';
		
		//GOOGLE VIDEOS
		$patterns[] = '/\[google\](.+?)\[\/google\]/';
		$replacements[] = '\\1';
			
		$comment = preg_replace($patterns, $replacements, $comment);
		
		
		// QUOTE
		$quotePattern = '#\[quote\s?name=\"([^\"\'\<\>\(\)]+)+\"\](<br\s?\/?\>)*(.*?)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '\\3';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}
		$quotePattern = '#\[quote[^\]]*?\](<br\s?\/?\>)*([^\[]+)(<br\s?\/?\>)*\[\/quote\]#i';
		$quoteReplace = '\\2';
		while(preg_match($quotePattern, $comment)) {
			$comment = preg_replace($quotePattern, $quoteReplace, $comment);
		}

		$comment = preg_replace('#\[\/?(b|i|u|s|url|img|list|quote|code)\]#', '', $comment);
		
		return $comment;
	}
	
	function getAvatar($user_id,$useremail=null)
	{
		RSCommentsHelper::readConfig();
		$avatar = RSCommentsHelper::getConfig('avatar');
		$db =& JFactory::getDBO();
		
		if (!$avatar) return '';
		$html = '';
		switch ($avatar)
		{
			// Gravatar
			case 'gravatar':
			$user = JFactory::getUser($user_id);
			$email = ($user_id == 0 && !is_null($useremail)) ? md5(strtolower(trim($useremail))) : md5(strtolower(trim($user->get('email'))));
			
			$html .= '<img src="http://www.gravatar.com/avatar/'.$email.'?d='.urlencode(JURI::root().'administrator/components/com_rscomments/assets/images/user.png').'" alt="Gravatar" class="rsc_avatar" />';
			
			break;
			
			// Community Builder
			case 'comprofiler':
			$db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='".(int) $user_id."'");
			$avatar = $db->loadResult();
			if (!$avatar)
				$html .= '<img src="'.JURI::root().'components/com_comprofiler/plugin/templates/default/images/avatar/tnnophoto_n.png" alt="Community Builder Avatar" class="rsc_avatar" />';
			else
				$html .= '<img src="'.JURI::root().'images/comprofiler/'.$avatar.'" alt="Community Builder Avatar" class="rsc_avatar" />';
			break;
			
			 // JomSocial
			case 'community':
				require_once( JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
				$user =& CFactory::getUser($user_id);
				$html .= '<img src="'.$user->getThumbAvatar().'" alt="JomSocial Avatar" class="rsc_avatar" />';
			break;
			
			//Kunena
			case 'kunena':
				$db->setQuery("SELECT avatar FROM #__kunena_users WHERE userid='".(int) $user_id."'");
				$avatar = $db->loadResult();
				
				if (!$avatar)
					$avatar = 's_nophoto.jpg';
				
				$html .= '<img src="'.JURI::root().'media/kunena/avatars/'.$avatar.'" alt="Kunena Avatar" class="rsc_avatar" />';
			break;
			
			//Fireboard
			case 'fireboard':
			$db->setQuery("SELECT avatar FROM #__fb_users WHERE userid='".(int) $user_id."'");
			$avatar = $db->loadResult();
			
			if (!$avatar)
				$avatar = 's_nophoto.jpg';
			
			$html .= '<img src="'.JURI::root().'images/fbfiles/avatars/'.$avatar.'" alt="Fireboard Avatar" class="rsc_avatar" />';
			break;
			
			//EasyBlog
			case 'easyblog':
				jimport('joomla.html.parameter');
				
				$db->setQuery("SELECT avatar FROM #__easyblog_users WHERE id = '".(int) $user_id."'");
				$avatar = $db->loadResult();
				
				$db->setQuery("SELECT parmas FROM #__easyblog_configs WHERE name = 'config'");
				$eparams = $db->loadResult();
				
				$eparams = new JParameter($eparams);
				$path = $eparams->get('main_avatarpath','images/easyblog_avatar/');
				
				if (empty($avatar) || $avatar == 'default.png')
					$html .= '<img src="'.JURI::root().'components/com_easyblog/assets/images/default.png" alt="EasyBlog Avatar" class="rsc_avatar" />';
				else
					$html .= '<img src="'.JURI::root().$path.$avatar.'" alt="EasyBlog Avatar" class="rsc_avatar" />';
			break;
		}
		
		return $html;
	}
	
	function createBBs($permissions)
	{	
		$bbcode = '';
		
		if (isset($permissions['bb_bold']) && $permissions['bb_bold'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/bold.gif" onclick="rsc_addTags(\'[b]\',\'[/b]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_italic']) && $permissions['bb_italic'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/italic.gif" onclick="rsc_addTags(\'[i]\',\'[/i]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_underline']) && $permissions['bb_underline'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/underline.gif" onclick="rsc_addTags(\'[u]\',\'[/u]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_stroke']) && $permissions['bb_stroke'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/stroke.gif" onclick="rsc_addTags(\'[s]\',\'[/s]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_quote']) && $permissions['bb_quote'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/quote.gif" onclick="rsc_addTags(\'[quote]\',\'[/quote]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_lists']) && $permissions['bb_lists'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/ordered.gif" onclick="rsc_createList(\'[LIST=1]\',\'[/LIST]\',\'rsc_comment\');" /> <img src="'.JURI::root().'components/com_rscomments/assets/images/unordered.gif" onclick="rsc_createList(\'[LIST]\',\'[/LIST]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_image']) && $permissions['bb_image'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/picture.gif" onclick="rsc_createImage(\'rsc_comment\',\''.JText::_('RSC_ADD_IMAGE',true).'\');" /> ';
		if (isset($permissions['bb_url']) && $permissions['bb_url'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/link.gif" onclick="rsc_createUrl(\'rsc_comment\',\''.JText::_('RSC_ADD_URL',true).'\');" /> ';
		if (isset($permissions['bb_code']) && $permissions['bb_code'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/code.gif" onclick="rsc_addTags(\'[code]\',\'[/code]\',\'rsc_comment\');" /> ';
		if (isset($permissions['bb_videos']) && $permissions['bb_videos'])
			$bbcode .= '<img src="'.JURI::root().'components/com_rscomments/assets/images/youtube.gif" onclick="rsc_addTags(\'[youtube]\',\'[/youtube]\',\'rsc_comment\');" /> <img src="'.JURI::root().'components/com_rscomments/assets/images/google.gif" onclick="rsc_addTags(\'[google]\',\'[/google]\',\'rsc_comment\');" /> ';
		
		return $bbcode;
	}
	
	function newlinetobr($text)
	{
		$text = str_replace("\r",'',$text);
		$text = str_replace("\n",'<br/>',$text);
		
		return $text;
	}
	
	function breakwords($comment)
	{
		RSCommentsHelper::readConfig();
		$length = RSCommentsHelper::getConfig('word_length');
		$length = empty($length) ? 15 : $length;
		$marker = ' ';

		$text = $comment;
		$text = preg_replace('#<img[^\>]+/>#isU', '', $text);
		$text = preg_replace('#<a.*?>(.*?)</a>#isU', '', $text);
		$text = preg_replace('#<object.*?>(.*?)</object>#isU', '', $text);
		$text = preg_replace('#<code.*?>(.*?)</code>#isU', '', $text);
		$text = preg_replace('#<embed.*?>(.*?)</embed>#isU', '', $text);
		$text = preg_replace('#(^|\s|\>|\()((http://|https://|news://|ftp://|www.)\w+[^\s\[\]\<\>\"\'\)]+)#i', '', $text);
		
		$matches = array();
		$matchCount = preg_match_all('#([^\s<>\'\"/\.\x133\x151\\-\?&%=\n\r\%]{'.$length.'})#iu', $text, $matches);
		
		for ($i = 0; $i < $matchCount; $i++) {
			$comment = preg_replace("#(".preg_quote($matches[1][$i], '#').")#iu", "\\1".$marker, $comment);
		}
		$comment = preg_replace('#('.preg_quote($marker, '#').'\s)#iu', " ", $comment);
		
		unset($matches);

		return $comment;
	}
	
	function ajaxpagination($content,$option,$id,$template,$pagination,$override=false)
	{
		jimport('joomla.application.router');
		require_once(JPATH_SITE.DS.'includes'.DS.'application.php');

		$config =& JFactory::getConfig();
		$u =& JFactory::getURI();
		$options['mode'] = $config->getValue('config.sef');
		$router =& JRouter::getInstance('site', $options);
		
		$pattern = '#href="(.*?)"#is';
		preg_match_all($pattern,$content,$matches);
		
		//get root
		$root = $u->toString(array('scheme','host'));
		
		$shPattern = '#\/Page-([0-9]+)#';
		$acePattern = '#\/page-([0-9]+)#';
		$issh404SEF = get_class($router) == 'shRouter';
		$isJoomSEF = get_class($router) == 'JRouterJoomsef';
		$isAceSEF = get_class($router) == 'JRouterAcesef';
		
		//if AceSef is installed do not show the ajax pagination
		if ($isAceSEF) return $content;
		
		$next = $pagination->limit;
		$next_position = count($matches[1]) - 2;
		$last = ($pagination->get('pages.total') -1) * $pagination->limit;
		$last_position = count($matches[1]) - 1;
		
		$joption = JRequest::getVar('option');
		
		if (!empty($matches))
		{
			foreach ($matches[1] as $i=> $url)
			{				
				$uri =& JURI::getInstance($url);
				$urivars = $router->parse($uri);
				$vars = array();
				foreach ($urivars as $var => $val)
					$vars[$var] = $val;
				
				// fix for sh404SEF
				if ($issh404SEF)
				{
					if (preg_match($shPattern, $url, $match))
					{
						$shPage = $match[1];
						$vars['limitstart'] = ($shPage - 1) * $pagination->limit;
					}
				
					if ($joption != 'com_rscomments' && !isset($vars['limitstart']))
					{
						if ($i == $next_position)
							$vars['limitstart'] = $next;
						elseif ($i == $last_position)
							$vars['limitstart'] = $last;
						else
							$vars['limitstart'] = ($i+1)*$pagination->limit;
					}
				}
				
				// fix for JoomSEF - if nothing in $_POST, tries to redirect but ends up in a loop
				// we add a "dummy" $_POST variable so that it doesn't try to redirect
				if ($isJoomSEF)
					$_POST['rscomments_dummy'] = 1;
				
				$vars['limitstart'] = isset($vars['limitstart']) ? (int) $vars['limitstart'] : 0;
				$pos = strpos($content, $matches[0][$i]);
				
				if ($issh404SEF)
					$content = substr_replace($content, 'href="javascript:void(0);" onclick="rsc_pagination(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\','.$vars['limitstart'].',\''.$option.'\',\''.$id.'\',\''.$template.'\', \''.(int) $override.'\')"', $pos, strlen($matches[0][$i]));
				else 
					$content = str_replace($matches[0][$i],'href="javascript:void(0);" onclick="rsc_pagination(\''.$root.'\',\''.JRoute::_('index.php?option=com_rscomments').'\','.$vars['limitstart'].',\''.$option.'\',\''.$id.'\',\''.$template.'\', \''.(int) $override.'\')"',$content);
			}
		}
		
		return $content;
	}
	
}
?>