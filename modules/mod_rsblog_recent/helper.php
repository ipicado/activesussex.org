<?php
/**
* @version 1.0.0
* @package RSBlog! 1.0.0
* @copyright (C) 2010-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modRsblogRecentHelper
{
	function getList(&$params)
	{
		$db =& JFactory::getDBO();
		
		$items = array();
		$date = JFactory::getDate();
		$join = '';
		$condition = '';
		
		//parameters
		$limit = $params->get('limit',0);
		$limit = $limit ? " LIMIT ".(int) $limit : '';
		$ordering = $params->get('ordering','DESC');
		$catid = $params->get('catid');
		
		$db->setQuery("SELECT `id` FROM `#__rsblog_categories` WHERE `published`='1'");
		$categories = $db->loadResultArray();
		
		if (!empty($categories))
		{
			$categories = implode(',',$categories);
			$categories = RSBlogHelper::usercategories($categories);			
			
			$condition = " AND pc.`cat_id` IN (".$db->getEscaped($categories).") AND pc.`cat_id` = ".$catid;
			$join = " LEFT JOIN `#__rsblog_posts_categories` pc ON pc.`post_id` = p.`id` ";
		}
		
		$days = (int) $params->get('days', 4);
		if (!$days)
			$days = 4;
		
		$db->setQuery("SELECT DISTINCT p.`id`, p.`title`, p.`alias`, p.`introtext` FROM `#__rsblog_posts` p ".$join." WHERE p.`published` = 1 AND p.`created_date` > '".($date->toUnix() - $days*86400)."' AND ( p.`publish_up` = ".$db->Quote($db->getNullDate())." OR p.`publish_up` <= ".$db->Quote($date->toMySQL()).") AND (p.`publish_down` = ".$db->Quote($db->getNullDate())." OR p.`publish_down` >= ".$db->Quote($date->toMySQL())." ) AND p.`password` = '' ".$condition." ORDER BY p.`created_date` ".$ordering." ".$limit);
		
		$items = $db->loadObjectList();

		return $items;
	}
}