/**
** Main functions for administration of ClickHeat
**
** @author Yvan Taviaud - Labsmedia.com
** @since 01/04/2007
**/

var currentAlpha = 80;
var lastDayOfMonth = 0;
var currentDate = [0, 0, 0, 0, 0];
var currentRange = 'd';
var currentWidth = 0;
var pleaseWait = '';
var cleanerRunning = '';
var isJsOkay = '';
var scriptPath = '';
var scriptIndexPath = '';

/** Returns the "top" value of an element */
function getTop(obj)
{
	if (obj.offsetParent != undefined)
	{
		return (obj.offsetTop + getTop(obj.offsetParent));
	}
	else
	{
		return obj.offsetTop;
	}
}

/** Resize the div relative to window height and selected screen size */
function resizeDiv()
{
	oD = document.documentElement != undefined && document.documentElement.clientHeight != 0 ? document.documentElement : document.body;
	iH = oD.innerHeight != undefined ? oD.innerHeight : oD.clientHeight;
	document.getElementById('overflowDiv').style.height = (iH < 300 ? 400 : iH) - getTop(document.getElementById('overflowDiv')) + 'px';
	/** Width of main display */
	iW = oD.innerWidth != undefined ? oD.innerWidth : oD.clientWidth;
	iW = iW < 300 ? 400 : iW;
	if (document.getElementById('formScreen').value == 0)
	{
		currentWidth = iW;
	}
	else
	{
		currentWidth = document.getElementById('formScreen').value - 5;
	}
	document.getElementById('overflowDiv').style.width = currentWidth + 'px';
	document.getElementById('webPageFrame').style.width = currentWidth - 25 + 'px';
}

/** Update calendar selected days */
function updateCalendar(day)
{
	if (day != undefined)
	{
		currentDate[0] = day;
	}
	currentDate[1] = currentDate[3];
	currentDate[2] = currentDate[4];
	if (currentRange == 'd')
	{
		min = currentDate[0];
		max = currentDate[0];
	}
	if (currentRange == 'm')
	{
		currentDate[0] = 1;
		min = 1;
		max = weekDays.length;
	}
	if (currentRange == 'w')
	{
		week = weekDays[currentDate[0]];
		min = 0;
		max = 0;
		for (d = 1; d < weekDays.length; d++)
		{
			if (weekDays[d] == week)
			{
				if (min == 0)
				{
					currentDate[0] = d;
					min = d;
				}
				max = d;
			}
		}
		/** Start was on the previous month */
		if (min == 1 && max != 7)
		{
			currentDate[0] = lastDayOfMonth - 6 + max;
			currentDate[1]--;
			if (currentDate[1] == 0)
			{
				currentDate[1] = 12;
				currentDate[2]--;
			}
		}
	}
	for (d = 1; d < weekDays.length; d++)
	{
		document.getElementById('clickheat-calendar-' + d).className = (d >= min && d <= max ? 'clickheat-calendar-on' : '');
	}
	for (i = 1; i < 7; i++)
	{
		if (document.getElementById('clickheat-calendar-10' + i) != undefined)
		{
			document.getElementById('clickheat-calendar-10' + i).className = (currentRange == 'w' && weekDays[min] == weekDays[1] ? 'clickheat-calendar-on' : '');
		}
		if (document.getElementById('clickheat-calendar-11' + i) != undefined)
		{
			document.getElementById('clickheat-calendar-11' + i).className = (currentRange == 'w' && weekDays[max] == weekDays[weekDays.length - 1] ? 'clickheat-calendar-on' : '');
		}
	}
	document.getElementById('clickheat-calendar-d').className = (currentRange == 'd' ? 'clickheat-calendar-on' : '');
	document.getElementById('clickheat-calendar-w').className = (currentRange == 'w' ? 'clickheat-calendar-on' : '');
	document.getElementById('clickheat-calendar-m').className = (currentRange == 'm' ? 'clickheat-calendar-on' : '');
	updateHeatmap();
}

/** Ajax object */
function getXmlHttp()
{
	var xmlhttp = false;
	try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
	catch (e)
	{
		try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	}
		catch (oc) { xmlhttp = null; }
	}
	if (!xmlhttp && typeof XMLHttpRequest != undefined) xmlhttp = new XMLHttpRequest();
	return xmlhttp;
}

/** Ajax request to update PNGs */
function updateHeatmap()
{
	var xmlhttp;
	document.getElementById('pngDiv').innerHTML = '&nbsp;<div style="line-height:20px"><span class="error">' + pleaseWait + '</span></div>';
	xmlhttp = getXmlHttp();
	var screen = 0;
	if (document.getElementById('formScreen').value == 0)
	{
		screen = -1 * currentWidth + 25;
	}
	else
	{
		screen = document.getElementById('formScreen').value;
	}
	xmlhttp.open('GET', scriptIndexPath + 'action=generate&page=' + document.getElementById('formPage').value + '&screen=' + screen + '&browser=' + document.getElementById('formBrowser').value + '&date=' + currentDate[2] + '-' + currentDate[1] + '-' + currentDate[0] + '&range=' + currentRange + '&heatmap=' + (document.getElementById('formHeatmap').checked ? '1' : '0') + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			document.getElementById('pngDiv').innerHTML = xmlhttp.responseText.replace(/_JAVASCRIPT_/, isJsOkay);
			document.getElementById('webPageFrame').height = document.getElementById('pngDiv').offsetHeight;
			changeAlpha(currentAlpha);
		}
	}
	xmlhttp.send(null);
}

/** Ajax request to show page layout */
function showPageLayout()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=layout&page=' + document.getElementById('formPage').value + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			document.getElementById('layoutDiv').innerHTML = xmlhttp.responseText;
			document.getElementById('layoutDiv').style.display = 'block';
			showRadioLayout();
		}
	}
	xmlhttp.send(null);
}

/** Ajax request to show javascript code */
function showJsCode()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=javascript&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			document.getElementById('layoutDiv').innerHTML = xmlhttp.responseText;
			document.getElementById('layoutDiv').style.display = 'block';
			updateJs();
		}
	}
	xmlhttp.send(null);
}

/** Hide page layout */
function hidePageLayout()
{
	document.getElementById('layoutDiv').style.display = 'none';
	document.getElementById('layoutDiv').innerHTML = '';
}

/** Update JS code in display */
function updateJs()
{
	var str = '';
	var language = (navigator.language !== undefined && navigator.language == 'fr' ? 'fr' : 'com');
	str += '&lt;script type="text/javascript" src="' + scriptPath + 'js/clickheat.js"&gt;&lt;/script&gt;\n';
	str += '&lt;script type="text/javascript"&gt;\n';
	str += 'clickHeatPage = ';
	if (document.getElementById('jsPage1').checked)
	{
		str += '\'<span class="error">ABC</span>\'';
	}
	if (document.getElementById('jsPage2').checked)
	{
		str += 'document.title';
	}
	if (document.getElementById('jsPage3').checked)
	{
		str += 'window.location.pathname';
	}
	str += ';\n';
	if (document.getElementById('jsQuota').value != 0)
	{
		str += 'clickHeatQuota = ' + document.getElementById('jsQuota').value.replace(/[^0-9]*/g, '') + ';\n';
	}
	if (scriptPath != '/clickheat/')
	{
		str += 'clickHeatServer = \'' + scriptPath + 'click.php\';\n';
	}
	str += 'initClickHeat();\n';
	str += '&lt;/script&gt;\n';
	if (document.getElementById('jsShowImage').checked)
	{
		str += '&lt;a href="http://www.labsmedia.' + language + '/clickheat/index.html" title="ClickHeat: clicks heatmap"&gt;&lt;img src="' + scriptPath + 'images/logo.png" width="80" height="15" border="0" alt="ClickHeat : track clicks" /&gt;&lt;/a&gt;';
	}
	else
	{
		str += '&lt;noscript&gt;&lt;a href="http://www.labsmedia.' + language + '/clickheat/index.html" title="ClickHeat: clicks heatmap"&gt;clickheat&lt;/a&gt;&lt;/noscript&gt;'
	}
	document.getElementById('clickheat-js').innerHTML = str;
}

/** Show layout's parameters */
function showRadioLayout()
{
	for (i = 0; i < 7; i++)
	{
		document.getElementById('layout-span-' + i).style.display = (document.getElementById('layout-radio-' + i).checked ? 'block' : 'none');
	}
}

/** Show layout's parameters */
function savePageLayout()
{
	for (i = 0; i < 7; i++)
	{
		if (document.getElementById('layout-radio-' + i).checked)
		{
			break;
		}
	}
	if (i == 7)
	{
		alert('Error');
		return false;
	}
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=layoutupdate&page=' + document.getElementById('formPage').value + '&url=' + document.getElementById('formUrl').value + '&left=' + document.getElementById('layout-left-' + i).value + '&right=' + document.getElementById('layout-right-' + i).value + '&center=' + document.getElementById('layout-center-' + i).value + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			if (xmlhttp.responseText != 'OK')
			{
				alert(xmlhttp.responseText);
			}
			hidePageLayout();
			loadIframe();
		}
	}
	xmlhttp.send(null);
}

/** Ajax request to get associated page in iframe */
function loadIframe()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=iframe&page=' + document.getElementById('formPage').value + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			if (document.getElementById('webPageFrame').src.search(/clickempty\.html$/) != -1)
			{
				document.getElementById('webPageFrame').src = xmlhttp.responseText;
				updateCalendar();
			}
			else
			{
				document.getElementById('webPageFrame').src = xmlhttp.responseText;
				updateHeatmap();
			}
		}
	}
	xmlhttp.send(null);
}

/** Hide iframe's flashes and iframes */
function cleanIframe()
{
	if (document.getElementById('webPageFrame').src.search(/clickempty\.html$/) != -1)
	{
		return true;
	}
	try
	{
		var currentIframe = document.getElementById('webPageFrame');
		if (currentIframe.contentDocument)
		{
			currentIframeContent = currentIframe.contentDocument;
		}
		else if (currentIframe.Document)
		{
			currentIframeContent = currentIframe.Document;
		}
		/** Hide iframes and flashes content */
		if (currentIframeContent == undefined)
		{
			return false;
		}
		newContent = currentIframeContent.body.innerHTML;
		oldPos = 0;
		while (true)
		{
			pos = newContent.search(/<(object|iframe)/i);
			pos2 = newContent.search(/<\/(object|iframe)>/i);
			if (pos == -1 || pos2 == -1 || pos == oldPos) break;
			pos2 += 9;
			found = newContent.substring(pos, pos2);
			width = found.match(/width=[^0-9]*(\d+)/);
			if (width == null) width = [0, 300];
			height = found.match(/height=[^0-9]*(\d+)/);
			if (height == null) height = [0, 150];
			newContent = newContent.substring(0, pos) + '<span style="margin:0; padding:' + Math.ceil(height[1] / 2) + 'px ' + Math.ceil(width[1] / 2) + 'px; line-height:' + (height[1] * 1 + 10) + 'px; border:1px solid #f00; background-color:#faa; font-size:0;">&nbsp;</span>&nbsp;test' + newContent.substring(pos2, newContent.length);
			oldPos = pos;
		}
		currentIframeContent.body.innerHTML = newContent;
	}
	catch(e) {}
}

/** Draw alpha selector */
function drawAlphaSelector(obj, max)
{
	var str = '';
	for (i = 0; i < max; i++)
	{
		grey = 255 - Math.ceil(i * 255 / max);
		alpha = Math.ceil(i * 100 / max);
		str += '<a href="#" id="alpha-level-' + alpha + '" onclick="changeAlpha(' + alpha + '); this.blur(); return false;" style="font-size:12px; border-top:1px solid #888; border-bottom:1px solid #888;' + (i == 0 ? ' border-left:1px solid #888;' : '') + '' + (i == max - 1 ? ' border-right:1px solid #888;' : '') + ' text-decoration:none; background-color:rgb(' + grey + ',' + grey + ',' + grey + ');">&nbsp;</a>';
	}
	document.getElementById(obj).innerHTML = str;
	/** Check that currentAlpha exists */
	while (document.getElementById('alpha-level-' + currentAlpha) == undefined)
	{
		currentAlpha--;
	}
}

/** Change Alpha on heatmap */
function changeAlpha(alpha)
{
	document.getElementById('alpha-level-' + currentAlpha).style.borderTop = '1px solid #888';
	document.getElementById('alpha-level-' + currentAlpha).style.borderBottom = '1px solid #888';
	currentAlpha = alpha;
	document.getElementById('alpha-level-' + currentAlpha).style.borderTop = '2px solid #55b';
	document.getElementById('alpha-level-' + currentAlpha).style.borderBottom = '2px solid #55b';
	for (i = 0; i < document.images.length; i++)
	{
		if (document.images[i].id.search(/^heatmap-\d+$/) == 0)
		{
			document.images[i].style.opacity = alpha / 100;
			if (document.body.filters != undefined)
			{
				document.images[i].style.filter = 'alpha(opacity:' + alpha + ')';
			}
		}
	}
}

/** Ajax request to show javascript code */
function runCleaner()
{
	document.getElementById('cleaner').innerHTML = cleanerRunning;
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=cleaner&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			if (xmlhttp.responseText == 'OK')
			{
				document.getElementById('cleaner').innerHTML = '';
			}
			else
			{
				document.getElementById('cleaner').innerHTML = xmlhttp.responseText;
				setTimeout("document.getElementById('cleaner').innerHTML = '';", 10000);
			}
		}
	}
	xmlhttp.send(null);
}

/** Ajax request to show latest available version */
function showLatestVersion()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=latest&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			document.getElementById('layoutDiv').innerHTML = xmlhttp.responseText;
			document.getElementById('layoutDiv').style.display = 'block';
			showRadioLayout();
		}
	}
	xmlhttp.send(null);
}