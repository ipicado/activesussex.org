var textarea;

function rsc_createImage(obj,text)
{
	textarea = document.getElementById(obj);
	var url = prompt(text,'http://');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (url != '' && url != null) 
	{
		if (document.selection) 
		{
			textarea.focus();
			var sel = document.selection.createRange();
			sel.text = '[img]' + url + '[/img]';
		}
		else 
		{
			var len = textarea.value.length;
			var start = textarea.selectionStart;
			var end = textarea.selectionEnd;
			
			var sel = textarea.value.substring(start, end);
			var rep = '[img]' + url + '[/img]';
			textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
			textarea.scrollTop = scrollTop;
			textarea.scrollLeft = scrollLeft;
		}
	}
}

function rsc_createUrl(obj,text)
{
	textarea = document.getElementById(obj);
	var url = prompt(text,'http://');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (url != '' && url != null) 
	{
		if (document.selection) 
		{
			textarea.focus();
			var sel = document.selection.createRange();
			
			if(sel.text=="")
				sel.text = '[url]'  + url + '[/url]';
			else 
				sel.text = '[url=' + url + ']' + sel.text + '[/url]';
					
		}
		else 
		{
			var len = textarea.value.length;
			var start = textarea.selectionStart;
			var end = textarea.selectionEnd;
			
			var sel = textarea.value.substring(start, end);
			
			if(sel=="")
				var rep = '[url]' + url + '[/url]';
			else
				var rep = '[url=' + url + ']' + sel + '[/url]';
		
			textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);	
			textarea.scrollTop = scrollTop;
			textarea.scrollLeft = scrollLeft;
		}
	}
}

function rsc_addTags(tag1,tag2,obj)
{
	textarea = document.getElementById(obj);
	// Code for IE
	if (document.selection) 
	{
		textarea.focus();
		var sel = document.selection.createRange();
		sel.text = tag1 + sel.text + tag2;
	}
	else 
    {  
		// Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

        var sel = textarea.value.substring(start, end);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;		
	}
}

function rsc_createList(tag1,tag2,obj)
{
	textarea = document.getElementById(obj);
	// Code for IE
	if (document.selection) 
	{
		textarea.focus();
		var sel = document.selection.createRange();
		var list = sel.text.split('\n');
	
		for(i=0;i<list.length;i++)
			list[i] = '[*]' + list[i];

		sel.text = tag1 + '\n' + list.join("\n") + '\n' + tag2;
	} 
	else
	// Code for Firefox
	{
		var len = textarea.value.length;
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var i;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;		
		var sel = textarea.value.substring(start, end);
		
		var list = sel.split('\n');
		
		for(i=0;i<list.length;i++)
			list[i] = '[*]' + list[i];
		
		var rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
}

function rsc_smiley(tag)
{
	textarea = document.getElementById('rsc_comment');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (document.selection) 
	{
		textarea.focus();
		var sel = document.selection.createRange();
		sel.text = tag;
	}
	else 
	{
		var len = textarea.value.length;
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		var sel = textarea.value.substring(start, end);
		var rep = tag;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
		textarea.focus();
	}
	
	var popup = document.getElementById('rsc_emoticons');
	popup.style.display = 'none';
}