<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.mail.helper');

class RSFormProValidations
{
	function none($value,$extra=null)
	{
		return true;
	}

	function email($email,$extra=null)
	{
		$email = trim($email);
		return JMailHelper::isEmailAddress($email);
	}
	function numeric($param,$extra=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if (strpos($extra,$param[$i]) === false && !is_numeric($param[$i]))
				return false;
				
		return true;
	}

	function alphanumeric($param,$extra = null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z0-9 ])#', $param[$i]))
				return false;
				
		return true;
	}

	function alpha($param,$extra=null)
	{
		if(strpos($param,"\n") !== false) 
			$param = str_replace(array("\r","\n"),'',$param);
			
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false && preg_match('#([^a-zA-Z ])#', $param[$i]))
				return false;
				
		return true;
	}

	function custom($param,$extra=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
			if(strpos($extra,$param[$i]) === false)
				return false;
				
		return true;
	}

	function password($param,$extra=null)
	{
		return true;
	}
	
	function uniqueEmail($param, $extra=null)
	{
		if(!RSFormProValidations::email($param,null))
		{
			return false;
		}
		else
		{
			//$formId = JRequest::getInt('formId');
			//mail ( 'stephenelford@me.com' , 'Form ID' , $formId );
			$formId = 6;
			$db = JFactory::getDBO();
			$param = $db->getEscaped($param);
			$db->setQuery("SELECT * FROM jos_rsform_submission_values WHERE `FieldName`='Email' AND `FieldValue`='".$param."' AND `FormId`='".$formId."'");
			$db->query();
			$invalid = $db->getNumRows();
			if ($invalid>0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		return false;
	}


}
?>