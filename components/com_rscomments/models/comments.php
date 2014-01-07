<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSCommentsModelComments extends JModel
{
	var $_data = null;
	var $_total = 0;
	var $_query = '';
	var $_pagination = null;
	var $_db = null;
	var $_id = null;
	var $_option = null;
	
	function __construct($id = null,$option = null,$pag=null)
	{
		parent::__construct();
		$this->_db =& JFactory::getDBO();
		$this->_id = $id;
		$this->_option = $option;
		$this->_query = $this->_buildQuery();
		
		$mainframe =& JFactory::getApplication();
		
		$pagination = $pag != null ? $pag : $mainframe->getCfg('list_limit');
		
		// Get pagination request variables 
		$limit = JRequest::getVar('limit', $pagination, '', 'int'); 
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int'); 
		// In case limit has been changed, adjust it 
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); 
		
		$this->setState('rscomments.comments.limit', $limit);
		$this->setState('rscomments.comments.limitstart', $limitstart);
	}
	
	function _buildQuery()
	{
		$user =& JFactory::getUser();
		$permissions = $this->getPermissions();
		
		if (RSCommentsHelper::is16())
		{
			$groups = JAccess::getGroupsByUser($user->id);
			$publish = ((isset($permissions['publish_comments']) && $permissions['publish_comments']) || in_array(8,$groups)) ? "" : " AND `published` = 1 ";
		} else 		
			$publish = ((isset($permissions['publish_comments']) && $permissions['publish_comments']) || $user->usertype == 'Super Administrator') ? "" : " AND `published` = 1 ";
		
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
		$order = $config->default_order;
		
		$query = "SELECT * FROM #__rscomments_comments WHERE `option` = '".$this->_db->getEscaped($this->_option)."' AND id = '".$this->_id."' ".$publish." ORDER BY `date` ".$order." ";
		
		return $query;
	}
	
	function getComments()
	{		
		if (empty($this->_data))
			$this->_data = $this->_getList($this->_query, $this->getState('rscomments.comments.limitstart'), $this->getState('rscomments.comments.limit'));
		
		return $this->_data;
	}
	
	function getTotal()
	{
		if (empty($this->_total))
			$this->_total = $this->_getListCount($this->_query); 
		
		return $this->_total;
	}
	
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('rscomments.comments.limitstart'), $this->getState('rscomments.comments.limit'));
		}
		
		return $this->_pagination;
	}
	
	/*
	* Get the comment details 
	*/
	
	function getComment()
	{
		$cid = JRequest::getVar('cid',0);
		$row =& JTable::getInstance('RSComments_Comments','Table');
		$row->load($cid);
		return $row;
		
	}
	
	/*
	* Get permissions
	*/
	
	function getPermissions()
	{
		$user =& JFactory::getUser();
		
		if (!RSCommentsHelper::is16())
		{
			$gid = $user->gid;
			if($gid == 0) $gid = 29;
		
			$this->_db->setQuery("SELECT `permissions` FROM #__rscomments_groups WHERE `gid` = ".$gid);
			$permissions = $this->_db->loadResult();
			
			if(!empty($permissions)) $permissions = unserialize($permissions); else $permissions = array();
			
		} else 
		{
			$groups = JAccess::getGroupsByUser($user->id);
			$permissions = 'a:6:{s:12:"new_comments";s:1:"1";s:7:"captcha";s:1:"0";s:13:"vote_comments";s:1:"1";s:8:"censored";s:1:"1";s:11:"check_names";s:1:"1";s:13:"flood_control";s:1:"1";}';
			$permissions = unserialize($permissions);
			
			//remove the public permission
			if (!$user->guest)
			{
				foreach ($groups as $i => $group)
					if ($group == 1) unset($groups[$i]);
			}
			
			$tmp = array();
			
			if (!empty($groups))
				foreach ($groups as $group)
				{
					$this->_db->setQuery("SELECT `permissions` FROM #__rscomments_groups WHERE `gid` = ".$group." AND `joomla` = 16 ");
					$permission = $this->_db->loadResult();
					if (!empty($permission)) $tmp[$group] = unserialize($permission);
				}
			
			if (!empty($tmp))
				foreach ($tmp as $group)
					foreach ($group as $key => $value)
						if ($value) $permissions[$key] = $value;
							else $permissions[$key] = 0;
		}
		
		return $permissions;
	}
	
	/*
	* Reject forbidden names 
	*/
	
	function forbiddenNames($name)
	{
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
		
		$return = false;
		$names = strtolower(trim($config->forbiden_names));
		
		if(!empty($names))
		{	
			$name = strtolower($name);
			$names = explode("\n",$names);
			foreach($names as $val)
				if(trim($val) == $name) $return = true;
		}
		return $return;
	}
	
	
	/*
	* Censor bad words 
	*/
	
	function censor($text)
	{
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
		
		$replace = trim($config->censored_words);
		$with = empty($config->replace_censored) ? '***' : $config->replace_censored;
		
		if (!empty($replace)) 
		{
			$replace = explode(',',$replace);
			
			foreach($replace as $value) 
			{
				if(empty($value)) continue;
				$text = preg_replace('#'.$value.'#is', $with, $text);
			}
		}
		return $text;
	}
	
	/*
	* Check for flood comments
	*/
	
	function flood($ip)
	{
		$db = JFactory::getDBO();
		
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
			
		$db->setQuery("SELECT COUNT(IdComment) FROM `#__rscomments_comments` WHERE `ip` = '".$ip."' AND NOW() < DATE_ADD(FROM_UNIXTIME(date), INTERVAL ".intval($config->flood_interval)." SECOND)");
		$result = $db->loadResult();
		if($result == 0) return true; else return false;
	}
	
	
	/*
	* Save function 
	*/
	
	function save()
	{
		jimport('joomla.mail.helper');
		
		$user =& JFactory::getUser();
		$db =& JFactory::getDBO();
		$cfg = new JConfig;
		$u =& JURI::getInstance();
		$post = JRequest::get('post');
		$permissions = $this->getPermissions();
		$row =& JTable::getInstance('RSComments_Comments','Table');
		$err = array();
		
		if(!isset($permissions['new_comments'])) return false;
		
		//get the config
		RSCommentsHelper::readConfig();
		$config = RSCommentsHelper::getConfig();
		
		if (!$row->bind($post))
			return JError::raiseWarning(500, $row->getError());
		
		$row->email = trim($row->email);
		$row->option = $post['obj_option'];
		$row->id = $post['obj_id'];
		
		if($post['IdComment'] == 0 || empty($post['IdComment']))
		{
			$row->uid = $user->id;
			$row->ip = $_SERVER['REMOTE_ADDR'];
			$row->date = time();
			$row->published = (isset($permissions['autopublish']) && $permissions['autopublish']) ? 1 : 0;
		}
		
		//check for flood commenting
		if(!$this->flood($row->ip) && (isset($permissions['flood_control']) && $permissions['flood_control']) && empty($row->IdComment)) $err['task'] = addslashes(JText::sprintf('RSC_WAIT_FOR_COMMENT',intval($config->flood_interval)));
		
		//check for comment length
		if(function_exists('mb_strlen'))
		{
			if (mb_strlen($row->comment,'UTF-8') < $config->min_comm_len) $err['comment'] = JText::_('RSC_COMMENT_TO_SHORT',true);		
			if (mb_strlen($row->comment,'UTF-8') > $config->max_comm_len) $err['comment'] = JText::_('RSC_COMMENT_TO_LONG',true);
		} else 
		{
			if (strlen(utf8_decode($row->comment)) < $config->min_comm_len) $err['comment'] = JText::_('RSC_COMMENT_TO_SHORT',true);		
			if (strlen(utf8_decode($row->comment)) > $config->max_comm_len) $err['comment'] = JText::_('RSC_COMMENT_TO_LONG',true);
		}
		
		//check for blocked users
		if (trim($config->blocked_users) != '')
		{
			$bad_users = explode("\n",$config->blocked_users);
			if (!empty($bad_users))
			foreach($bad_users as $bad_user)
				if ($bad_user == $user->username) $err['task'] = JText::_('RSC_BLOCKED_USER',true);
		}
		
		//remove any bad words
		if(isset($permissions['censored']) && $permissions['censored'])
		{
			$row->comment = $this->censor($row->comment);
			$row->subject = $this->censor($row->subject);
		}
		
		//check for forbidden names
		if(isset($permissions['check_names']) && $permissions['check_names'])
			if($this->forbiddenNames($row->name)) $err['name'] = JText::_('RSC_BAD_NAME',true);
		
		
		//form validation	
		if(empty($row->name)) $err['name'] = JText::_('RSC_NO_NAME',true);
		
		if(!empty($row->email) && !JMailHelper::isEmailAddress($row->email))
			$err['email'] = JText::_('RSC_NO_VALID_EMAIL',true);
		elseif(empty($row->email)) $err['email'] = JText::_('RSC_NO_EMAIL',true);
		
		if(empty($row->comment)) $err['comment'] = JText::_('RSC_NO_COMMENT',true);
		
		if ($config->terms)
		{
			if (empty($post['rsc_terms'])) $err['rsc_terms'] = JText::_('RSC_AGREE_TERMS',true);
		}
		
		if(isset($permissions['captcha']) && $permissions['captcha'])
		{
			if($config->captcha == 0)
			{
				$captcha_image = new JSecurImage();
				$valid = $captcha_image->check($post['captcha']);
				if (!$valid) $err['captcha'] = JText::_('RSC_INVALID_CAPTCHA',true);
			} else 
			{
				require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'recaptcha'.DS.'recaptchalib.php');
				$privatekey = $config->rec_private;
				
				$response = JReCAPTCHA::checkAnswer($privatekey, @$_SERVER['REMOTE_ADDR'], @$post['recaptcha_challenge_field'], @$post['recaptcha_response_field']);
				if ($response === false || !$response->is_valid)
				{
					$err['recaptcha_response_field'] = JText::_('RSC_INVALID_CAPTCHA',true);
				}
			}
		}
		if(!empty($err)) return $err;
		
		if($config->enable_bbcode == 0 && !isset($permissions['bbcode']))
			$row->comment = RSCommentsHelper::cleanComment($row->comment);
		
		if (RSCommentsHelper::is16())
			$groups = JAccess::getGroupsByUser($user->id);
		
		//akismet protection
		if(!empty($config->akismet_key) && ($user->usertype != 'Super Administrator' || (RSCommentsHelper::is16() && !in_array(8,$groups))))
		{
			$data = array();
			$url = JURI::root();
			$data['author'] = $row->name; 
			$data['email'] = $row->email;
			$data['body'] = $row->comment;
			$data['permalink'] = $url;
			$data['website'] = $config->enable_title_field == 1 ? $row->website : '';
			
			$akismet = new Akismet($url, $config->akismet_key, $data);
			if(!$akismet->errorsExist())
			{
				if($akismet->isSpam()) $row->published = 0;
			}
		}
		
		//Email notifications
		if($config->email_notification)
		{
			if(empty($row->IdComment) || $row->IdComment == 0 )
			{
				$preview = '<a href="'.JURI::root().base64_decode($row->url).'" target="_blank">'.JURI::root().base64_decode($row->url).'</a>'; 
				$message = $config->comments_notification;
				$replace = array('{username}','{email}','{ip}','{link}','{message}');
				$comment = RSCommentsHelper::parseComment($row->comment,$permissions);
				$comment = RSCommentsEmoticons::cleanText($comment);
				$with = array(empty($user->username) ? JText::_('RSC_GUEST') : $user->username,$user->email,$row->ip,$preview,$comment);
				$message = str_replace($replace,$with,$message);
				
				$emails = $config->notification_emails;
				if (!empty($emails))
				{
					$emails = explode(',',$emails);
					if (!empty($emails))
						foreach ($emails as $email)
							JUtility::sendMail($cfg->mailfrom, $cfg->fromname, $email, JText::_('RSC_NOTIFICATION_SUBJECT'), $message, 1, null, null, null);
				}
			}
		}
		
		$filter = new JFilterInput();
		$row->name = $filter->clean($row->name);
		$row->name = $db->getEscaped($row->name);
		$row->name = htmlentities($row->name,ENT_COMPAT,'UTF-8');
		
		$row->subject = $filter->clean($row->subject);
		$row->subject = $db->getEscaped($row->subject);
		$row->subject = htmlentities($row->subject,ENT_COMPAT,'UTF-8');
		
		if($this->checkURL($row->website))
			$row->website = $db->getEscaped($row->website);
		else
			$row->website = '';
		
		if ($row->store())
		{
			//send subscriptions
			$this->_db->setQuery("SELECT `name` , `email` FROM `#__rscomments_subscriptions` WHERE `id` = '".$row->id."' AND `option` = '".$row->option."' ");
			$subscribers = $this->_db->loadObjectList();
			if(!empty($subscribers))
			foreach($subscribers as $subscriber)
			{
				$msg = $config->comments_subscriptions;
				$preview = '<a href="'.JURI::root().base64_decode($row->url).'" target="_blank">'.JURI::root().base64_decode($row->url).'</a>'; 
				$replace = array('{name}','{author}','{message}','{link}');
				$with = array($subscriber->name,'"'.$row->name.'"',RSCommentsHelper::parseComment($row->comment,$permissions),$preview);
				$msg = str_replace($replace,$with,$msg);
				JUtility::sendMail($cfg->mailfrom, $cfg->fromname, $subscriber->email, JText::_('RSC_NEW_COMMENT_SUBJECT'), $msg, 1, null, null, null);
			}
		
			return $row;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	/*
	* Remove comment 
	*/
	
	function remove($cid)
	{
		jimport('joomla.filesystem.file');
		
		$owner = $this->isAuthor($cid); 
		$permissions = $this->getPermissions();
		if( (!isset($permissions['delete_own_comment']) && !$owner ) || !isset($permissions['delete_comments'])) return;
		$download_folder = JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'assets'.DS.'files';
		
		$this->_db->setQuery("SELECT file FROM #__rscomments_comments WHERE `IdComment` = ".$cid."");
		$file = $this->_db->loadResult();
		
		if (!empty($file))
			JFile::delete($download_folder.DS.$file);
		
		$this->_db->setQuery("DELETE FROM #__rscomments_votes WHERE `IdComment` = ".$cid."");
		$this->_db->query();
		$this->_db->setQuery("DELETE FROM #__rscomments_comments WHERE `IdComment` = ".$cid."");
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	/*
	* Publis/Unpublish comments 
	*/
	
	function publish($cid, $publish=1)
	{
		$permissions = $this->getPermissions();
		if(!isset($permissions['publish_comments'])) return;
		
		$publish = (int) $publish;
		$cid = (int) $cid;

		$query = "UPDATE #__rscomments_comments SET `published` = '".$publish."' WHERE `IdComment` = ".$cid;
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return $cid;
	}
	
	/*
	* Vote function 
	*/
	
	function vote($cid,$state=1)
	{
		$user =& JFactory::getUser();
		$permissions = $this->getPermissions();
		if(!isset($permissions['vote_comments'])) return;
		
		$state = (int) $state;
		$cid = (int) $cid;

		$state = ($state == 1) ? 'positive' : 'negative';
		
		if ($user->get('guest'))
			$query = "SELECT `IdVote` FROM #__rscomments_votes WHERE `IdComment`= '".$cid."' AND `ip`='".$_SERVER['REMOTE_ADDR']."'";
		else
			$query = "SELECT `IdVote` FROM #__rscomments_votes WHERE `IdComment`= '".$cid."' AND (`ip`='".$_SERVER['REMOTE_ADDR']."' OR `uid`='".$user->id."')";
		
		$this->_db->setQuery($query);
		$voted = $this->_db->loadResult();
		if(empty($voted))
		{		
			$query = "INSERT INTO `#__rscomments_votes` SET `IdComment` = '".$cid."' , `uid` = '".$user->id."' , `ip` = '".$this->_db->getEscaped($_SERVER['REMOTE_ADDR'])."' , `value` = '".$state."'";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		
		return $cid;
	}
	
	/*
	* Check for author 
	*/
	
	function isAuthor($id)
	{
		$user =& JFactory::getUser();
		$this->_db->setQuery("SELECT uid FROM #__rscomments_comments WHERE `IdComment` = ".$id);
		$uid = $this->_db->loadResult();
		if($uid == $user->id) return true; else return false;
	}
	
	/*
	* Subscribe function
	*/
	
	function subscribe()
	{
		$post = JRequest::get('post');
		$user =& JFactory::getUser();
		
		$this->_db->setQuery("SELECT COUNT(`IdSubscription`) FROM `#__rscomments_subscriptions` WHERE `email` = '".$this->_db->getEscaped($user->email)."' AND `option` = '".$this->_db->getEscaped($post['opt'])."' AND `id` = '".$this->_db->getEscaped($post['cid'])."' LIMIT 1 ");
		$already = $this->_db->loadResult();
		
		if($already == 0) 
		{
			$this->_db->setQuery("INSERT INTO `#__rscomments_subscriptions` SET `email` = '".$this->_db->getEscaped($user->email)."' , `option` = '".$this->_db->getEscaped($post['opt'])."' , `id` = '".$this->_db->getEscaped($post['cid'])."' , `name` = '".$user->name."' ");			
			$this->_db->query();
			return true;
		} else return false;
	}
	
	/*
	* Unsubscribe function
	*/
	
	function unsubscribe()
	{
		$post = JRequest::get('post');
		$user =& JFactory::getUser();
		
		$this->_db->setQuery("SELECT COUNT(`IdSubscription`) FROM `#__rscomments_subscriptions` WHERE `email` = '".$this->_db->getEscaped($user->email)."' AND `option` = '".$this->_db->getEscaped($post['opt'])."' AND `id` = '".$this->_db->getEscaped($post['cid'])."' LIMIT 1 ");
		$already = $this->_db->loadResult();
		
		if($already == 0)
			return false;
		else 
		{
			$this->_db->setQuery("DELETE FROM `#__rscomments_subscriptions` WHERE `email` = '".$this->_db->getEscaped($user->email)."' AND `option` = '".$this->_db->getEscaped($post['opt'])."' AND `id` = '".$this->_db->getEscaped($post['cid'])."' ");
			$this->_db->query();
			return true;
		}
	}
	
	/*
	* Is the user subscribed ? 
	*/
	
	function getSubscriber($id,$option)
	{
		$user =& JFactory::getUser();
		
		$this->_db->setQuery("SELECT COUNT(`IdSubscription`) FROM `#__rscomments_subscriptions` WHERE `email` = '".$this->_db->getEscaped($user->email)."' AND `option` = '".$this->_db->getEscaped($option)."' AND `id` = '".$this->_db->getEscaped($id)."' LIMIT 1 ");
		$res = $this->_db->loadResult();
		
		return $res > 0 ? true : false;
	}
	
	function checkURL($url)
	{
		// SCHEME
		$urlregex = "#^(https?|ftp)\:\/\/";

		// USER AND PASS (optional)
		$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

		// HOSTNAME OR IP
		$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*"; // http://x = allowed (ex. http://localhost, http://routerlogin)
		//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+"; // http://x.x = minimum
		//$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}"; // http://x.xx(x) = minimum
		//use only one of the above

		// PORT (optional)
		$urlregex .= "(\:[0-9]{2,5})?";
		// PATH (optional)
		$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
		// GET Query (optional)
		$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
		// ANCHOR (optional)
		$urlregex .= "(\#[a-z_.-][a-z0-9+\$_.-]*)?\$#is";

		// check
		if (preg_match($urlregex, $url)) 
			return true;
		else return false;
	}
	
}
?>