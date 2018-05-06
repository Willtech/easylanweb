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
include('../includes/functions.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Top Menu - easylanweb Intranet</title>
<link href="../css/master.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/scripts/site.js"></script>
</head>

<body class="topmenu" onLoad="getframed();">
<div style="width:100%; position:relative; top: -10px;">
&nbsp;&nbsp;<a href="/" target="_top">Home</a>&nbsp;&nbsp;
<?
$dirlist = listdir('/');
$dircount = count($dirlist);
$count = 0; 
while ($count < $dircount) {
	echo ('|&nbsp;&nbsp;<a href="menu.php?go='.$dirlist[$count].'" target="menuFrame">'.$dirlist[$count].'</a>&nbsp;&nbsp;');
	$count++;
	}
?>
</div>
</body>
</html>
