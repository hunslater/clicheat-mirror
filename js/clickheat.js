/**
ClickHeat : Suivi et analyse des clics / Tracking and clicks analysis

@author Yvan Taviaud - LabsMedia - www.labsmedia.com/clickheat/
@since 27/10/2006
@update 01/03/2007 - Yvan Taviaud : correctif Firefox (Károly Marton)
@update 23/03/2007 - Yvan Taviaud : protection de 2 secondes entre chaque clic, et X clics maximum par page
@update 18/05/2007 - Yvan Taviaud : suppression de clickHeatPage, ajout de clickHeatGroup et clickHeatSite

Tested under :
Windows 2000 - IE 6.0
Linux - Firefox 2.0.0.1, Konqueror 3.5.5, IE 7
*/

/** Main function */
function catchClickHeat(e)
{
	/** Use a try{} to avoid showing errors to users */
	try
	{
		if (clickHeatQuota == 0)
		{
			if (clickHeatDebug == true)
			{
				alert('Click not logged: quota reached');
			}
			return true;
		}
		if (clickHeatGroup == '')
		{
			if (clickHeatDebug == true)
			{
				alert('Click not logged: group name empty (clickHeatGroup)');
			}
			return true;
		}
		/** Look for the real event */
		if (e == undefined)
		{
			e = window.event;
			c = e.button;
			element = e.srcElement;
		}
		else
		{
			c = e.which;
			element = null;
		}
		if (c == 0)
		{
			if (clickHeatDebug == true)
			{
				alert('Click not logged: no button pressed');
			}
			return true;
		}
		/** Filter for same iframe (focus on iframe => popup ad => close ad => new focus on same iframe) */
		if (element != null && element.tagName.toLowerCase() == 'iframe')
		{
			if (element.sourceIndex == clickHeatLastIframe)
			{
				if (clickHeatDebug == true)
				{
					alert('Click not logged: same iframe (happens when a click on iframe occured opening a popup and popup is closed)');
				}
				return true;
			}
			clickHeatLastIframe = element.sourceIndex;
		}
		else
		{
			clickHeatLastIframe = -1;
		}
		x = e.clientX;
		y = e.clientY;
		d = document.documentElement != undefined && document.documentElement.clientHeight != 0 ? document.documentElement : document.body;
		scrollx = window.pageXOffset == undefined ? d.scrollLeft : window.pageXOffset;
		scrolly = window.pageYOffset == undefined ? d.scrollTop : window.pageYOffset;
		w = window.innerWidth == undefined ? d.clientWidth : window.innerWidth;
		h = window.innerHeight == undefined ? d.clientHeight : window.innerHeight;
		/** Is the click in the viewing area? Not on scrollbars */
		if (x > w || y > h)
		{
			if (clickHeatDebug == true)
			{
				alert('Click not logged: out of document (should be a click on scrollbars under IE)');
			}
			return true;
		}
		/** Check if last click was at least 1 second ago */
		clickTime = new Date();
		if (clickTime.getTime() - clickHeatTime < 1000)
		{
			if (clickHeatDebug == true)
			{
				alert('Click not logged: at least 1 second between clicks');
			}
			return true;
		}
		clickHeatTime = clickTime.getTime();
		if (clickHeatQuota > 0)
		{
			clickHeatQuota = clickHeatQuota - 1;
		}
		/** Also the User-Agent is not the best value to use, it's the only one that gives the real browser */
		b = navigator.userAgent != undefined ? navigator.userAgent.toLowerCase().replace(/-/g, '') : '';
		b0 = b.replace(/^.*(firefox|kmeleon|safari|msie|opera).*$/, '$1');
		if (b == b0 || b0 == '') b0 = 'unknown';
		params = 's=' + clickHeatSite + '&g=' + clickHeatGroup + '&x=' + (x + scrollx) + '&y=' + (y + scrolly) + '&w=' + w + '&b=' + b0 + '&c=' + c + '&random=' + Date();
		/** Local request? Try an ajax call */
		var sent = false;
		if (clickHeatServer.substring(0, 4) != 'http')
		{
			var xmlhttp = false;
			try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
			catch (e)
			{
				try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	}
				catch (oc) { xmlhttp = null; }
			}
			if (!xmlhttp && typeof XMLHttpRequest != undefined) xmlhttp = new XMLHttpRequest();
			if (xmlhttp)
			{
				if (clickHeatDebug == true)
				{
					xmlhttp.onreadystatechange = function()
					{
						if (xmlhttp.readyState == 4)
						{
							if (xmlhttp.status == 200)
							{
								alert('Click logged to ' + clickHeatServer + ' with the following parameters:\nx = ' + (x + scrollx) + ' (' + x + 'px from left + ' + scrollx + 'px of horizontal scrolling)\ny = ' + (y + scrolly) + ' (' + y + 'px from top + ' + scrolly + 'px of vertical scrolling)\nwidth = ' + w + '\nbrowser = ' + b0 + '\nclick = ' + c + '\n\nServer answer: ' + xmlhttp.responseText);
							}
							else if (xmlhttp.status == 404)
							{
								alert('click.php was not found at: ' + (clickHeatServer != '' ? clickHeatServer : '/clickheat/click.php') + ' please set clickHeatServer value');
							}
							else
							{
								alert('click.php returned a status code ' + xmlhttp.status + ' with the following error: ' + xmlhttp.responseText);
							}
						}
					}
				}
				xmlhttp.open('GET', clickHeatServer + '?' + params, true);
				xmlhttp.setRequestHeader('Connection', 'close');
				xmlhttp.send(null);
				sent = true;
			}
		}
		if (sent == false)
		{
			var clickHeatImg = new Image();
			clickHeatImg.src = clickHeatServer + '?' + params;
			if (clickHeatDebug == true)
			{
				alert('Click logged at ' + clickHeatServer + ' with the following parameters:\nx = ' + (x + scrollx) + ' (' + x + 'px from left + ' + scrollx + 'px of horizontal scrolling)\ny = ' + (y + scrolly) + ' (' + y + 'px from top + ' + scrolly + 'px of vertical scrolling)\nwidth = ' + w + '\nbrowser = ' + b0 + '\nclick = ' + c + '\n\nserver response: unknown (not an Ajax call)');
			}
		}
	}
	catch(e)
	{
		if (clickHeatDebug == true)
		{
			alert('An error occurred while processing click');
		}
	}
	return true;
}

var clickHeatGroup = '';
var clickHeatSite = '';
var clickHeatServer = '';
var clickHeatLastIframe = -1;
var clickHeatTime = 0;
var clickHeatQuota = -1;
var clickHeatDebug = (window.location.href.search(/debugclickheat/) != -1);
function initClickHeat()
{
	if (clickHeatGroup == '' || clickHeatServer == '')
	{
		if (clickHeatDebug == true)
		{
			alert('ClickHeat NOT initialised: either clickHeatGroup or clickHeatServer is empty');
		}
		return false;
	}
	/** If current website has the same domain as the script, we remove the domain so that the call is made using Ajax */
	domain = window.location.href.match(/http:\/\/[^/]+\//);
	if (domain != null && clickHeatServer.substring(0, domain[0].length) == domain[0])
	{
		clickHeatServer = clickHeatServer.substring(domain[0].length - 1, clickHeatServer.length)
	}
	/** Add onmousedown event */
	if (typeof document.onmousedown == 'function')
	{
		currentFunc = document.onmousedown;
		document.onmousedown = function(e) { catchClickHeat(e); return currentFunc(e); }
	}
	else
	{
		document.onmousedown = catchClickHeat;
	}
	/** Add onfocus event on iframes (mostly ads) - Does NOT work with Gecko-powered browsers, because onfocus doesn't exist on iframes */
	iFrames = document.getElementsByTagName('iframe');
	for (i = 0; i < iFrames.length; i++)
	{
		if (typeof iFrames[i].onfocus == 'function')
		{
			currentFunc = iFrames[i].onfocus;
			iFrames[i].onfocus = function(e) { catchClickHeat(e); return currentFunc(e); }
		}
		else
		{
			iFrames[i].onfocus = catchClickHeat;
		}
	}
	if (clickHeatDebug == true)
	{
		alert('ClickHeat initialised with:\nsite = ' + clickHeatSite + '\ngroup = ' + clickHeatGroup + '\nserver = ' + clickHeatServer + '\nquota = ' + (clickHeatQuota == -1 ? 'unlimited' : clickHeatQuota));
	}
}