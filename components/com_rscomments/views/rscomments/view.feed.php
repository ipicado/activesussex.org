<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'models'.DS.'comments.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_rscomments'.DS.'helpers'.DS.'rscomments.php');

class RSCommentsViewRSComments extends JView
{

	function display()
	{
		$doc		=& JFactory::getDocument();
		$id			= JRequest::getVar('id');
		$option		= JRequest::getVar('opt');
		
		$class = new RSCommentsModelComments($id,$option);
		$rows  = $class->getComments();
		
		foreach ($rows as $row )
		{
			// load individual item creator class
			$item = new JFeedItem();
			$item->title 		= !empty($row->subject) ? $row->subject : 'no subject';
			$item->link 		= JURI::base().base64_decode($row->url);
			$item->description 	= RSCommentsHelper::parseComment($row->comment);
			$item->date			= date('r',$row->date);
			$item->category   	= 'Comments';

			// loads item info into rss array
			$doc->addItem( $item );
		}
	}
}
?>