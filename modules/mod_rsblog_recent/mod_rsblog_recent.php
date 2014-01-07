<?php
/**
* @version 1.0.0
* @package RSBlog! 1.0.0
* @copyright (C) 2010-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

//include RSBlog helper
if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsblog'.DS.'helpers'.DS.'rsblog.php'))
{
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsblog'.DS.'helpers'.DS.'rsblog.php');
	require_once(JPATH_SITE.DS.'components'.DS.'com_rsblog'.DS.'helpers'.DS.'route.php');
}
else return;

// Get the categories
$items	= modRsblogRecentHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_rsblog_recent', $params->get('layout', 'default'));