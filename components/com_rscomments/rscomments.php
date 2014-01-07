<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'akismet'.DS.'akismet.class.php');

RSCommentsHelper::readConfig();

// See if this is a request for a specific controller
$controller = JRequest::getCmd('controller');
$controllers = array('comments');
if (!empty($controller) && in_array($controller, $controllers))
{
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	$controller = 'RSCommentsController'.$controller;
	$RSCommentsController = new $controller();
}
else
	$RSCommentsController = new RSCommentsController();
	
$RSCommentsController->execute(JRequest::getCmd('task'));

// Redirect if set
$RSCommentsController->redirect();
?>