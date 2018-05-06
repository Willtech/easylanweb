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

if (empty($_GET['go'])) {
Header( "HTTP/1.1 302 Found" ); 
Header( "Location: ../home.php" );
exit;
}
include('../includes/functions.php');

$filepath = $_GET['go'];
$go = explode("/", $filepath);
$hmax = count($go);
$file = $go[$hmax-1];
$title = explode(".", $file);
$content = false;
switch ($title[1]) {
	case "txt":
		$content = parsefile($filepath);
	break;
	case "htm":
		$page = grabfile($filepath);
	break;
	case "html":
		$page = grabfile($filepath);
	break;
	case "mht":
		$content = grabfile($filepath);
	break;
	case "jpg":
		$imgtag = '<center><img src="../content/'.$filepath.'"></center>';
	break;
	case "JPG":
		$imgtag = '<center><img src="../content/'.$filepath.'"></center>';
	break;
	case "gif":
		$imgtag = '<center><img src="../content/'.$filepath.'"></center>';
	break;
	case "png":
		$imgtag = '<center><img src="../content/'.$filepath.'"></center>';
	break;
	default:
		$content = parsefile($filepath);
	break;
}
if ($page) {
	echo ($page);
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Page Body - easylanweb Intranet</title>
<link href="../css/body.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/scripts/site.js"></script>
</head>

<body onLoad="getframed();">
<h3><?=$title[0]?></h3>
<p>
<?
if ($content) {
 echo($content);
} else {
 echo ($imgtag);
?>
</p>
<p><strong>File: </strong><?=$file?> - <strong>Open [ <a href="<?="../content/$filepath"?>">Here</a> / <a href="<?="../content/$filepath"?>" target="_blank">New Window</a> ]</strong></p>
<?
}
?>
</body>
</html>
