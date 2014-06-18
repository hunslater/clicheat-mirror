/** Code by www.labsmedia.com */
function catchClickHeat(e){try{showClickHeatDebug('Gathering click data...');if(clickHeatQuota==0){showClickHeatDebug('Click not logged: quota reached');return true;}
if(clickHeatGroup==''){showClickHeatDebug('Click not logged: group name empty (clickHeatGroup)');return true;}
if(e==undefined){e=window.event;c=e.button;element=e.srcElement;}
else{c=e.which;element=null;}
if(c==0){showClickHeatDebug('Click not logged: no button pressed');return true;}
if(element!=null&&element.tagName.toLowerCase()=='iframe'){if(element.sourceIndex==clickHeatLastIframe){showClickHeatDebug('Click not logged: same iframe (happens when a click on iframe occured opening a popup and popup is closed)');return true;}
clickHeatLastIframe=element.sourceIndex;}
else{clickHeatLastIframe=-1;}
var x=e.clientX;var y=e.clientY;var w=clickHeatDocument.clientWidth!=undefined?clickHeatDocument.clientWidth:window.innerWidth;var h=clickHeatDocument.clientHeight!=undefined?clickHeatDocument.clientHeight:window.innerHeight;var scrollx=window.pageXOffset==undefined?clickHeatDocument.scrollLeft:window.pageXOffset;var scrolly=window.pageYOffset==undefined?clickHeatDocument.scrollTop:window.pageYOffset;
if(x>w||y>h){showClickHeatDebug('Click not logged: out of document (should be a click on scrollbars)');return true;}
clickTime=new Date();if(clickTime.getTime()-clickHeatTime<1000){showClickHeatDebug('Click not logged: at least 1 second between clicks');return true;}
clickHeatTime=clickTime.getTime();if(clickHeatQuota>0){clickHeatQuota=clickHeatQuota-1;}
params='s='+clickHeatSite+'&g='+clickHeatGroup+'&x='+(x+scrollx)+'&y='+(y+scrolly)+'&w='+w+'&b='+clickHeatBrowser+'&c='+c+'&random='+Date();showClickHeatDebug('Ready to send click data...');
var sent=false;if(clickHeatServer.substring(0, 4)!='http'){var xmlhttp=false;try { xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}
catch (e){try { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
catch (oc) { xmlhttp=null;}}
if(!xmlhttp&&typeof XMLHttpRequest!=undefined) xmlhttp=new XMLHttpRequest();if(xmlhttp){if(clickHeatDebug==true){xmlhttp.onreadystatechange=function(){if(xmlhttp.readyState==4){if(xmlhttp.status==200){showClickHeatDebug('Click recorded at '+clickHeatServer+' with the following parameters:<br />x='+(x+scrollx)+' ('+x+'px from left+'+scrollx+'px of horizontal scrolling)<br />y='+(y+scrolly)+' ('+y+'px from top+'+scrolly+'px of vertical scrolling)<br />width='+w+'<br />browser='+clickHeatBrowser+'<br />click='+c+'<br />site='+clickHeatSite+'<br />group='+clickHeatGroup+'<br /><br />Server answer: '+xmlhttp.responseText);}
else if(xmlhttp.status==404){showClickHeatDebug('click.php was not found at: '+(clickHeatServer!=''?clickHeatServer:'/clickheat/click.php')+' please set clickHeatServer value');}
else{showClickHeatDebug('click.php returned a status code '+xmlhttp.status+' with the following error: '+xmlhttp.responseText);}}}}
xmlhttp.open('GET', clickHeatServer+'?'+params, true);xmlhttp.setRequestHeader('Connection', 'close');xmlhttp.send(null);sent=true;}}
if(sent==false){if(clickHeatDebug==true){showClickHeatDebug('Click recorded at '+clickHeatServer+' with the following parameters:<br />x='+(x+scrollx)+' ('+x+'px from left+'+scrollx+'px of horizontal scrolling)<br />y='+(y+scrolly)+' ('+y+'px from top+'+scrolly+'px of vertical scrolling)<br />width='+w+'<br />browser='+clickHeatBrowser+'<br />click='+c+'<br />site='+clickHeatSite+'<br />group='+clickHeatGroup+'<br /><br />Server answer:<br />'+'<iframe src="'+clickHeatServer+'?'+params+'" width="400" height="30"></iframe>');}
else{var clickHeatImg=new Image();clickHeatImg.src=clickHeatServer+'?'+params;}}}
catch(e){showClickHeatDebug('An error occurred while processing click (Javascript error): '+e.message);}
return true;}
var clickHeatGroup='';var clickHeatSite='';var clickHeatServer='';var clickHeatLastIframe=-1;var clickHeatTime=0;var clickHeatQuota=-1;var clickHeatBrowser='';var clickHeatDocument='';var clickHeatDebug=(window.location.href.search(/debugclickheat/)!=-1);function initClickHeat(){if(clickHeatDebug==true){document.body.innerHTML=document.body.innerHTML+'<div id="clickHeatDebuggerDiv" style="padding:5px;display:none;position:absolute;top:10px;left:10px;border:1px solid #888;background-color:#eee;z-index:99;"><strong>ClickHeat debug: <a href="#" onmouseover="document.getElementById(\'clickHeatDebuggerDiv\').style.display=\'none\';return false">Rollover to close</a></strong><br /><br /><span id="clickHeatDebuggerSpan"></span></div>';}
if(clickHeatGroup==''||clickHeatServer==''){showClickHeatDebug('ClickHeat NOT initialised: either clickHeatGroup or clickHeatServer is empty');return false;}
domain=window.location.href.match(/http:\/\/[^/]+\//);if(domain!=null&&clickHeatServer.substring(0, domain[0].length)==domain[0]){clickHeatServer=clickHeatServer.substring(domain[0].length-1, clickHeatServer.length)}
if(typeof document.onmousedown=='function'){currentFunc=document.onmousedown;document.onmousedown=function(e) { catchClickHeat(e);return currentFunc(e);}}
else{document.onmousedown=catchClickHeat;}
iFrames=document.getElementsByTagName('iframe');for (i=0;i<iFrames.length;i++){if(typeof iFrames[i].onfocus=='function'){currentFunc=iFrames[i].onfocus;iFrames[i].onfocus=function(e) { catchClickHeat(e);return currentFunc(e);}}
else{iFrames[i].onfocus=catchClickHeat;}}
clickHeatDocument=document.documentElement!=undefined&&document.documentElement.clientHeight!=0?document.documentElement:document.body;
var b=navigator.userAgent!=undefined?navigator.userAgent.toLowerCase().replace(/-/g, ''):'';clickHeatBrowser=b.replace(/iceweasel/, 'firefox').replace(/^.*(firefox|kmeleon|safari|msie|opera).*$/, '$1');if(b==clickHeatBrowser||clickHeatBrowser=='') clickHeatBrowser='unknown';showClickHeatDebug('ClickHeat initialised with:<br />site='+clickHeatSite+'<br />group='+clickHeatGroup+'<br />server='+clickHeatServer+'<br />quota='+(clickHeatQuota==-1?'unlimited':clickHeatQuota)+'<br /><br />browser='+clickHeatBrowser);}
function showClickHeatDebug(str){if(clickHeatDebug==true){document.getElementById('clickHeatDebuggerSpan').innerHTML=str;document.getElementById('clickHeatDebuggerDiv').style.display='block';}}