<?php
/**
* @version 1.0.0
* @package RSComments! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsEmoticons
{	
	function createEmoticons()
	{
		$emoticons = RSCommentsEmoticons::setEmoticons();
		$html = '';
		foreach($emoticons as $tag => $img)
			$html .= '<a href="javascript:void(0);" onclick="rsc_smiley(\''.$tag.'\')"><img src="'.$img.'" alt="'.$tag.'" /></a><span style="margin:6px"></span>';
		
		return $html;
	}
	
	function setEmoticons()
	{
		$emoticons = array();
		$emoticons[':confused:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/confused.gif';
		$emoticons[':cool:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/cool.gif';
		$emoticons[':cry:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/cry.gif';
		$emoticons[':laugh:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/laugh.gif';
		$emoticons[':lol:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/lol.gif';
		$emoticons[':normal:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/normal.gif';
		$emoticons[':blush:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/redface.gif';
		$emoticons[':rolleyes:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/rolleyes.gif';
		$emoticons[':sad:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sad.gif';
		$emoticons[':shocked:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/shocked.gif';
		$emoticons[':sick:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sick.gif';
		$emoticons[':sleeping:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/sleeping.gif';
		$emoticons[':smile:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/smile.gif';
		$emoticons[':surprised:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/surprised.gif';
		$emoticons[':tongue:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/tongue.gif';
		$emoticons[':unsure:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/unsure.gif';
		$emoticons[':whistle:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/whistling.gif';
		$emoticons[':wink:'] = JURI::root().'components/com_rscomments/assets/images/emoticons/wink.gif';
		
		return $emoticons;
	}
	
	function cleanText($text)
	{
		$emoticons = RSCommentsEmoticons::setEmoticons();
		
		foreach($emoticons as $tag => $img)
			$text = str_replace($tag,'<img src="'.$img.'" alt="'.str_replace(':','',$tag).'" />',$text);
			
		return $text;
	}
}
?>