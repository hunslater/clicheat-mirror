/**
ClickHeat : Suivi et analyse des clics / Tracking and clicks analysis

@author Yvan Taviaud - LabsMedia - www.labsmedia.com
@since 27/10/2006

Tested under Windows XP, with the following browsers :
Kmeleon 1.02, Firefox 2.0 (Linux too), IE7, IE6.0 (Win 2000), Opera 9.02
*/

/** Main function */
function catchClickHeat(e)
{

	/** Use a try{} to avoid showing errors to users */
	try
	{
		/** Look for the real event */
		if (e == undefined)
		{
			e = window.event;
			b = e.button;
			element = e.srcElement;
		}
		else
		{
			b = e.which;
			element = null;
		}
		/** Filter for same iframe (focus on iframe => popup ad => close ad => new focus on same iframe) */
		if (element != null && element.tagName.toLowerCase() == 'iframe')
		{
			if (element.sourceIndex == clickHeatLastIframe)
			{
				return true;
			}
			clickHeatLastIframe = element.sourceIndex;
		}
		else
		{
			/** Is it a left-click (not on iframe) ? */
			if (b != 1 && iFrameNumber == -1) return true;
		}
		x = e.clientX;
		y = e.clientY;
		d = document.documentElement != undefined && document.documentElement.clientHeight != 0 ? document.documentElement : document.body;
		scrollx = d.scrollLeft != undefined ? d.scrollLeft : window.pageXOffset;
		scrolly = d.scrollTop != undefined ? d.scrollTop : window.pageYOffset;
		w = d.innerWidth != undefined ? d.innerWidth : d.clientWidth;
		h = d.innerHeight != undefined ? d.innerHeight : d.clientHeight;
		/** Is the click in the viewing area ?*/
		if (x > w || y > h) return true;
		/** Also the User-Agent is not the best value to use, it's the only one that gives the real browser */
		b = navigator.userAgent != undefined ? navigator.userAgent.toLowerCase().replace(/-/g, '') : '';
		b0 = b.replace(/^.*(firefox|kmeleon|safari|msie|opera).*$/, '$1');
		if (b == b0 || b0 == '') b0 = 'unknown';
		params = 'p=' + clickHeatPage + '&x=' + (x + scrollx) + '&y=' + (y + scrolly) + '&w=' + w + '&b=' + b0 + '&random=' + Date();
		/** Local request ? Try an ajax call */
		if (clickHeatServer == '' || clickHeatServer.substring(0, 4) != 'http')
		{
			if (clickHeatServer == '')
			{
				clickHeatServer = '/clickheat/click.php';
			}
			try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
			catch (e)
			{
				try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	}
				catch (oc) { xmlhttp = null; }
			}
			if (!xmlhttp && typeof XMLHttpRequest != undefined) xmlhttp = new XMLHttpRequest();
			if (xmlhttp)
			{
				xmlhttp.open('GET', clickHeatServer + '?' + params, true);
				xmlhttp.setRequestHeader('Connection', 'close');
				xmlhttp.send(null);
				clickHeatServer = '';
			}
		}
		if (clickHeatServer != '')
		{
			var clickHeatImg = new Image();
			clickHeatImg.src = clickHeatServer + '?' + params;
		}
	} catch(e) {}
	return true;
}

var clickHeatPage = '';
var clickHeatServer = '';
var clickHeatLastIframe = -1;
function initClickHeat(page, server)
{
	clickHeatPage = page;
	clickHeatServer = (server == undefined ? '' : server);
	/** Add onmousedown event */
	if (typeof document.onmousedown == 'function')
	{
		currentFunc = document.onmousedown;
		document.onmousedown = function(e) { currentFunc(e); catchClickHeat(e); return true; }
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
			iFrames[i].onfocus = function(e) { currentFunc(e); catchClickHeat(e); return true; }
		}
		else
		{
			iFrames[i].onfocus = catchClickHeat;
		}
	}
}