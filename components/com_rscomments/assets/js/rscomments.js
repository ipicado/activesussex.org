/*
 * RSComments! javascript functions
*/

var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};

function rsc_upload(id)
{
	//set the id to the upload frame
	var rsframe = document.getElementById('rsc_frame');
	var innerDoc = rsframe.contentDocument || rsframe.contentWindow.document;
	innerDoc.getElementById('cid').value = id;
	innerDoc.getElementById('frameform').submit();
}

function rsc_refresh(root,url)
{
	var loader = document.getElementById('rsc_loading_form');
	var container = document.getElementById('rscomments_big_container');	


	xml=buildXmlHttp();
	params = 'tmpl=component&randomTime='+Math.random();
	
	xml.open("POST", root + url, true);

	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

	xml.onreadystatechange = function() 
	{//Call a function when the state changes.
		if (xml.readyState==4)
		{
			loader.style.display = 'none';
			document.getElementById('rsc_global_pagination').innerHTML = '';
			container.innerHTML = xml.responseText;			
		}
	}
	xml.send(params);	
	loader.style.display = '';
}

function rsc_edit(root,url,id)
{
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=edit&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			response = xml.responseText;
			response = response.split('{s3p}');
			document.rscommentsForm.IdComment.value = response[0];
			document.rscommentsForm.name.value = response[1];
			document.rscommentsForm.email.value = response[2];
			if (isset(document.rscommentsForm.subject))
				document.rscommentsForm.subject.value = response[3];
			if (isset(document.rscommentsForm.website))
				document.rscommentsForm.website.value = response[4];
			document.rscommentsForm.comment.value = response[5];
			document.rscommentsForm.name.disabled = false;
			document.rscommentsForm.email.disabled = false;
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_quote(root,url,name,id)
{
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=quote&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			response = xml.responseText;
			document.rscommentsForm.comment.value = '[quote name="'+name+'"]'+response + '[/quote]';
			document.rscommentsForm.IdComment.value = '';
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_delete(root,url,id)
{
	var container = document.getElementById('rscomment'+id);
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=remove&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			container.parentNode.removeChild(container);
		}
    }
	xml.send(params);
	loader.style.display = '';
	
}

function rsc_publish(root,url,id)
{
	var container = document.getElementById('rsc_publish'+id);
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=publish&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			container.innerHTML = xml.responseText;
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_unpublish(root,url,id)
{
	var container = document.getElementById('rsc_publish'+id);
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=unpublish&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			container.innerHTML = xml.responseText;
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_pos(root,url,id)
{
	var container = document.getElementById('rsc_voting'+id);
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=voteup&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			container.innerHTML = xml.responseText;
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_neg(root,url,id)
{
	var container = document.getElementById('rsc_voting'+id);
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=votedown&cid='+id+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			container.innerHTML = xml.responseText;
		}
    }
	xml.send(params);
	loader.style.display = '';
}

function rsc_save(root,url,url2,upload)
{
	var fields;
	var loader = document.getElementById('rsc_loading_form');
	var container = document.getElementById('rscomments_big_container');	


	xml=buildXmlHttp();
	
	var params = new Array();
	for (i=0; i<document.rscommentsForm.elements.length; i++)
	{
		//remove the error
		removeClass(document.rscommentsForm.elements[i],'rsc_error');
		
		var fname = document.rscommentsForm.elements[i].name;
		var fvalue = document.rscommentsForm.elements[i].value;
		
		// don't send an empty value
		if (fname.length == 0) continue;
		
		if (fname.indexOf('rsc_terms') != -1 )
		{
			if (document.getElementById(fname).checked)
				fvalue = 1;
			else
				fvalue = 0;
		}
		
		params.push(fname + '=' + encodeURIComponent(fvalue));
	}
	
	params = params.join('&');
	params = params + '&format=raw';
	
	xml.open("POST", root + url, true);

	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

	xml.onreadystatechange = function() 
	{//Call a function when the state changes.
		if (xml.readyState==4)
		{
			loader.style.display = 'none';
			var response = xml.responseText;
			response = response.split('|');
			
			if(response[0] == 'err')
			{
				alert(response[1]);
				fields = response[2];
				fields = fields.split('\n');					
				for(i=0;i<fields.length;i++)
				{
					if(typeof fields[i] == 'undefined') continue;
					eval('document.rscommentsForm.' + fields[i] + '.className += " rsc_error";');
				}
			} 
			else 
			{
				document.getElementById('rsc_global_pagination').innerHTML = '';
				container.innerHTML = response[1];
				document.rscommentsForm.reset();
				document.rscommentsForm.IdComment.value = '';
				
				//upload files
				if (parseInt(upload) == 1)
					rsc_upload(response[0]);
			}
			
			sign = (url.indexOf('?') == -1) ? '?' : '&';
			if(document.getElementById('submit_captcha_image')) document.getElementById('submit_captcha_image').src = root+url2+sign+'sid=' + Math.random();
			if(document.getElementById('rsc_recaptcha')) 
			{
				document.getElementById('recaptcha_response_field').value = '';
				Recaptcha.reload();
			}
		}
	}
	xml.send(params);	
	loader.style.display = '';
}

function isset () 
{
    var a = arguments,
        l = a.length,
        i = 0,
        undef;

    if (l === 0) {
        throw new Error('Empty isset');
    }

    while (i !== l) {
        if (a[i] === undef || a[i] === null) {
            return false;
        }
        i++;
    }
    return true;
}

function hasClass(ele,cls) {
	return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}

function removeClass(ele,cls) {
	if (hasClass(ele,cls)) {
		var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
		ele.className=ele.className.replace(reg,' ');
	}
}

function rsc_comment_cnt(field,cntfield,maxlimit) 
{				
	if (field.value.length > maxlimit)
		field.value = field.value.substring(0, maxlimit);
	else
	document.getElementById(cntfield).innerHTML = maxlimit - field.value.length;
}

function checkEmail(str) 
{
	var at="@"
	var dot="."
	var lat=str.indexOf(at)
	var lstr=str.length
	var ldot=str.indexOf(dot)
	if (str.indexOf(at)==-1)
	   return false

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr)
	   return false

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr)
		return false

	if (str.indexOf(at,(lat+1))!=-1)
		return false

	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot)
		return false

	if (str.indexOf(dot,(lat+2))==-1)
		return false

	if (str.indexOf(" ")!=-1)
		return false

	return true					
}

function rsc_refresh_captcha(root,url)
{
	sign = (url.indexOf('?') == -1) ? '?' : '&';
	
	document.getElementById('submit_captcha_image').src = root+url+sign+'sid=' + Math.random();
	return false;
}

function rsc_show_emoticons(what)
{
	var popup = document.getElementById('rsc_emoticons');
	popup.style.display = 'block';
}

function rsc_check(e)
{
	var target = (e && e.target) || (event && event.srcElement);
	var obj = document.getElementById('rsc_emoticons');
	var obj2 = document.getElementById('rsc_emoti_on');
	rsc_checkParent(target)?obj.style.display='none':null;
	target==obj2?obj.style.display='block':null;
}

function rsc_checkParent(t)
{
	while(t.parentNode)
	{
		if(t==document.getElementById('rsc_emoticons')) return false 
		t=t.parentNode
	}
	return true
}


function rsc_subscribe(root,url,id,option)
{	
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=subscribe&cid='+id+'&opt='+option+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			response = xml.responseText;
			response = response.split('|');
			document.getElementById('rsc_subscr').innerHTML = response[1];
			alert(response[0]);
		}
    }
	xml.send(params);
	loader.style.display = '';
	
}

function rsc_unsubscribe(root,url,id,option)
{	
	var loader = document.getElementById('rsc_loading');
	var params = 'controller=comments&task=unsubscribe&cid='+id+'&opt='+option+'&format=raw&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			response = xml.responseText;
			response = response.split('|');
			document.getElementById('rsc_subscr').innerHTML = response[1];
			alert(response[0]);
		}
    }
	xml.send(params);
	loader.style.display = '';
	
}

function rsc_pagination(root,url,page,option,id,template,override)
{
	var loader = document.getElementById('rsc_loading');
	var params = 'task=pagination&override=' + override +  '&page=' + page + '&content=' + option + '&id=' + id + '&rsctemplate=' + template + '&limitstart=' + page + '&pagination=0&randomTime='+Math.random();
	xml=buildXmlHttp();

	xml.open('POST',root + url,true);
	

	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			loader.style.display = 'none';
			var response = xml.responseText.split('{sep}');
			
			document.getElementById('rsc_global_pagination').innerHTML = response[1];
			document.getElementById('rscomments_big_container').innerHTML = response[0];
		}
    }
	xml.send(params);
	loader.style.display = '';
	
}

function buildXmlHttp()
{
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}