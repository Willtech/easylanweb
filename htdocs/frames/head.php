<?php
/* ******************************************* */
/*                                             */
/* This file is part of the "Intranet" package */
/* Produced for: {licence}                     */
/*                                             */
/* You are free to modify this source code     */
/* provided this notice and any tracking       */
/* mechanism remain intact and operational.    */
/*                                             */
/* This original source code and including any */
/* derivative works are copyright and may not  */
/* be distributed, copied, installed or        */
/* otherwise used (except as required for      */
/* archive or backup) without written          */
/* authorisation in advance                    */
/*                                             */
/* Source Code produced by Willtech 2008       */
/*                                             */
/* ******************************************* */
clearstatcache();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Top Banner - easylanweb Intranet</title>
<link href="../css/master.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/scripts/site.js"></script>
</head>

<body class="banner" onLoad="getframed();">
<script LANGUAGE="JavaScript"> 
<!-- 
//Work out correct time to update 
//Grab time from this server 
var cridlandnow = new Date("<? echo date("M d Y G:i:s"); ?>"); 
var cridlandcorrection = (new Date() - cridlandnow); 
//Only update every 30 secs 
timeID=window.setTimeout("cridlandtimeupdate();", 500); 

function cridlandtimeupdate() { 
var weekday=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"); 
var monthname=new Array("January","February","March","April","May","June","July","August","September","October","November","December"); 
//Get current date 
cridlandnow1 = new Date(); 
//Apply the correction 
var expdate = cridlandnow1.getTime(); 
expdate -= cridlandcorrection; 
cridlandnow1.setTime(expdate); 
cridlandhours = cridlandnow1.getHours(); 
if (cridlandhours>=13) {cridlandhours-=12;} 
if (cridlandnow1.getHours()>=12) {cridlandAorP="pm";} 
else 
{cridlandAorP="am"; } 
cridlandminutes = cridlandnow1.getMinutes(); 
cridlandseconds = cridlandnow1.getSeconds();
if (cridlandminutes < 10) {cridlandminutes = "0" + cridlandminutes}; 
if (cridlandseconds < 10) {cridlandseconds = "0" + cridlandseconds};
cridlandc=cridlandhours+":"+cridlandminutes+":"+cridlandseconds+" "+cridlandAorP+" - "+weekday[cridlandnow1.getDay()]+" "+cridlandnow1.getDate()+" "+monthname[cridlandnow1.getMonth()]+" "+cridlandnow1.getFullYear(); 

if (document.all) { 
//This is IE or Opera 
document.all['cridlandtime'].innerHTML = cridlandc; 
} else { 
//This is Mozilla 
document.getElementById("cridlandtime").innerHTML = cridlandc; 
} 
timeID=window.setTimeout("cridlandtimeupdate();",15000); 
} 
// --></script> 
<center><div style=" position:relative; top: -10px; float:right;"><span id='cridlandtime' style="font-weight:normal;"><? echo date("g.ia")." - ".date("l j F Y");?></span></div><br>
<table width="980" style=" position:relative; top: -20px;">
<tr><td align="left" valign="middle"><img src="../images/logoBlue.png" alt="easylanweb Logo" width="217" height="90"></td>
<td align="center" valign="middle"><h1>easylanweb</h1><h2>Intranet</h2></td><td align="right" valign="middle">
<img src="../images/myHospBlue.png" alt="My Hospital" height="90" ></td>
</tr></table></center>
</body>
</html>
