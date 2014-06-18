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
var jsAdminCookie = '';
var hideIframes = true;
var hideFlashes = true;
var isPmvModule = false;
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
	xmlhttp.open('GET', scriptIndexPath + 'action=generate&group=' + document.getElementById('formGroup').value + '&screen=' + screen + '&browser=' + document.getElementById('formBrowser').value + '&date=' + currentDate[2] + '-' + currentDate[1] + '-' + currentDate[0] + '&range=' + currentRange + '&heatmap=' + (document.getElementById('formHeatmap').checked ? '1' : '0') + '&rand=' + Date(), true);
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

/** Ajax request to show group layout */
function showGroupLayout()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=layout&group=' + document.getElementById('formGroup').value + '&rand=' + Date(), true);
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

/** Hide group layout */
function hideGroupLayout()
{
	document.getElementById('layoutDiv').style.display = 'none';
	document.getElementById('layoutDiv').innerHTML = '';
}

/** Update JS code in display */
function updateJs()
{
	var str = '';
	var language = (navigator.language !== undefined && navigator.language == 'fr' ? 'fr' : 'com');
	var addReturn = document.getElementById('jsShort').checked ? '' : '<br />';
	str += '&lt;script type="text/javascript" src="';
	str += scriptPath + 'js/clickheat.js"&gt;&lt;/script&gt;' + addReturn;
	if (language == 'fr')
	{
		linkList = ['&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Analyse de trafic&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Analyse comportementale des internautes&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Analyse comportement internautes&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Outils d\'analyse d\'audience&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Carte température page web&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Analyse des clics&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Optimisation de l\'ergonomie&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/clickheat/index.html"&gt;Optimisation ergonimique&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils marketing&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils webmaster&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils référencement&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Monétisation de contenu&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Optimisation de site&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Optimisation de trafic&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Marketing web&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils seo&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils open source&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils webmaster gratuits&lt;/a&gt;', '&lt;a href="http://www.labsmedia.fr/index.html"&gt;Outils gratuits webmaster&lt;/a&gt;'];
	}
	else
	{
		linkList = ['&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Traffic analysis&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Click analysis&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Surfer navigation analysis&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Navigational analysis&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Landing page optimization&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Ergonomy optimization&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Web design optimisation&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Heat map generator&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Open source heat map&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/clickheat/index.html"&gt;Open source traffic analysis&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Open source tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Webmaster tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Marketing tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Free marketing tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Open source marketing tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Seo tools&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Traffic optimization&lt;/a&gt;', '&lt;a href="http://www.labsmedia.com/index.html"&gt;Traffic monetization&lt;/a&gt;'];
	}
	if (document.getElementById('jsShowImage').checked)
	{
		str += '&lt;a href="http://www.labsmedia.' + language + '/clickheat/index.html" title="ClickHeat: clicks heatmap"&gt;&lt;img src="' + scriptPath + 'images/logo.png" width="80" height="15" border="0" alt="ClickHeat : track clicks" /&gt;&lt;/a&gt;' + addReturn;
	}
	else
	{
		rand = Math.floor(Math.random() * linkList.length);
		str += '&lt;noscript&gt;' + linkList[rand] + '&lt;/noscript&gt;' + addReturn;
	}
	str += '&lt;script type="text/javascript"&gt;&lt;!--<br />';
	str += 'clickHeatSite = ';
	/** PMV form */
	if (document.getElementById('form_site') != undefined)
	{
		str += document.getElementById('form_site').site.value.replace(/[^a-z0-9\-_\.]+/gi, '.');
	}
	else
	{
		str += '\'<span class="error">' + document.getElementById('jsSite').value.replace(/[^a-z0-9\-_\.]+/gi, '.') + '</span>\'';
	}
	str += ';' + addReturn + 'clickHeatGroup = ';
	if (document.getElementById('jsGroup1').checked)
	{
		str += '\'<span class="error">' + document.getElementById('jsGroup').value.replace(/[^a-z0-9\-_\.]+/gi, '.') + '</span>\'';
	}
	if (document.getElementById('jsGroup2').checked)
	{
		str += 'document.title';
	}
	if (document.getElementById('jsGroup3').checked)
	{
		str += 'window.location.pathname';
	}
	str += ';' + addReturn;
	if (document.getElementById('jsQuota').value != 0)
	{
		str += 'clickHeatQuota = <span class="error">' + document.getElementById('jsQuota').value.replace(/[^0-9]*/g, '') + '</span>;' + addReturn;
	}
	str += 'clickHeatServer = \'' + scriptPath + 'click' + (isPmvModule == true ? 'pmv' : '') + '.php\';' + addReturn;
	str += 'initClickHeat(); //--&gt;<br />';
	str += '&lt;/script&gt;';
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
function saveGroupLayout()
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
	xmlhttp.open('GET', scriptIndexPath + 'action=layoutupdate&group=' + document.getElementById('formGroup').value + '&url=' + document.getElementById('formUrl').value + '&left=' + document.getElementById('layout-left-' + i).value + '&right=' + document.getElementById('layout-right-' + i).value + '&center=' + document.getElementById('layout-center-' + i).value + '&rand=' + Date(), true);
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			if (xmlhttp.responseText != 'OK')
			{
				alert(xmlhttp.responseText);
			}
			hideGroupLayout();
			loadIframe();
		}
	}
	xmlhttp.send(null);
}

/** Ajax request to get associated group in iframe */
function loadIframe()
{
	var xmlhttp;
	xmlhttp = getXmlHttp();
	xmlhttp.open('GET', scriptIndexPath + 'action=iframe&group=' + document.getElementById('formGroup').value + '&rand=' + Date(), true);
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
	if (hideIframes == false && hideFlashes == false)
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
		if (hideIframes == false)
		{
			reg = 'object';
		}
		else
		{
			if (hideFlashes == false)
			{
				reg = 'iframe';
			}
			else
			{
				reg = 'object|iframe';
			}
		}
		startReg = new RegExp('<(' + reg + ')', 'i');
		endReg = new RegExp('<\/(' + reg + ')', 'i');
		while (true)
		{
			pos = newContent.search(startReg);
			pos2 = newContent.search(endReg);
			if (pos == -1 || pos2 == -1 || pos == oldPos || pos > pos2) break;
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

/** Shows main panel */
function showPanel()
{
	var div = isPmvModule ? 'contenu' : 'adminPanel';
	if (document.getElementById(div).style.display != 'none')
	{
		return true;
	}
	if (isPmvModule)
	{
		document.getElementById('loggued').style.display = 'block';
	}
	document.getElementById(div).style.display = 'block';
	document.getElementById('divPanel').innerHTML = '<img src="' + scriptPath + 'images/arrow-up.png" width="11" height="6" alt="" />';
	resizeDiv();
}
/** Hides main panel */
function hidePanel()
{
	var div = isPmvModule ? 'contenu' : 'adminPanel';
	if (isPmvModule)
	{
		document.getElementById('loggued').style.display = 'none';
	}
	document.getElementById(div).style.display = 'none';
	document.getElementById('divPanel').innerHTML = '<img src="' + scriptPath + 'images/arrow-down.png" width="11" height="6" alt="" />';
	resizeDiv();
}

/** Reverse the state of the admin cookie (used not to log the clicks for admin user) */
function adminCookie()
{
	if (confirm(jsAdminCookie))
	{
		document.cookie = 'clickheat-admin=; expires=Fri, 27 Jul 2001 01:00:00 UTC; path=/';
	}
	else
	{
		var date = new Date();
		date.setTime(date.getTime() + 365 * 86400 * 1000);
		document.cookie = 'clickheat-admin=1; expires=' + date.toGMTString() + '; path=/';
	}
}