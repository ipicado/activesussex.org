<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2010-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

function RSCommentsBuildRoute( &$query )
{
	// The output array, which will be parsed in the next function
	$segments = array();
	
	//set default view
	$query['view'] = 'rsblog';
		
		
		if (isset($query['task']))
			switch ($query['task'])
			{
				case 'terms':
					$segments[] = 'terms';
				break;
				
				case 'captcha':
					$segments[] = 'captcha';
				break;
				
				case 'upload':
					$segments[] = 'upload';
				break;
				
				case 'uploadfile':
					$segments[] = 'uploadfile';
				break;
				
				case 'download':
					$segments[] = 'download';
				break;
				
				case 'refresh':
					$segments[] = 'refresh';
				break;
			}
	
	unset($query['view'],$query['layout'],$query['controller'],$query['task'],$query['tmpl']);
	
	return $segments;
}

function RSCommentsParseRoute($segments)
{
	$query = array();
	//Replacing the ':' which is by default with '-' in the segments
	$segments[0] = str_replace(':','-',$segments[0]);
	
	switch ($segments[0])
	{	
		case 'terms':
			$query['task'] = 'terms';
			$query['tmpl'] = 'component';
		break;
		
		case 'captcha':
			$query['task'] = 'captcha';
		break;
		
		case 'upload':
			$query['task'] = 'upload';
			$query['tmpl'] = 'component';
		break;
		
		case 'uploadfile':
			$query['task'] = 'uploadfile';
			$query['tmpl'] = 'component';
		break;
		
		case 'download':
			$query['task'] = 'download';
			$query['tmpl'] = 'component';
		break;
		
		case 'refresh':
			$query['task'] = 'refresh';
			$query['tmpl'] = 'component';
		break;
	}
	
	return $query;
}