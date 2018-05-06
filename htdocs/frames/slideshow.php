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
if (empty($_GET[go])) {
Header( "HTTP/1.1 302 Found" ); 
Header( "Location: ../home.php" );
exit;
}
include('../includes/functions.php');
include_once('../includes/cache-kit.php');
	$cache_active = true;
	$cache_folder = 'cache/';
	$cache_time = 600; // 31536000 a year, 2628000 a month, 606462 a week, 86400 a day, 60 = 1 minute 
	
	$site_url = $_SERVER["HTTP_HOST"];
	$full_url = "http://".$site_url.$_SERVER["REQUEST_URI"];

	$result = acmeCache::fetch($full_url, $cache_time);
	if(!$result){
$folderpath = $_GET[go];
$temp = explode("/", $folderpath);
$title = $temp[(count($temp)-2)];
$piclist = listfile($folderpath);
$piccount = count($piclist);
$imageURLs = "'../content$folderpath$piclist[0]'";
$temp = explode(".", $piclist[0]);
$imageCaptions = "'$temp[0]'";
$imgplace = 1;
$emb = "";
while ($imgplace < $piccount) {
	$temp = explode(".", $piclist[$imgplace]);
	if (($temp[1] == "jpg") or ($temp[1] == "JPG")) {
		$imageCaptions .= ",'$temp[0]'";
		$imageURLs .= ",'../content$folderpath$piclist[$imgplace]'";
	} else {
		$emb = '<div style="float:right"><embed src="../content'.$folderpath.$piclist[$imgplace].'" autostart="true" height="30" width="140" volume="20"></div>';
	}
	$imgplace++;
}
$result  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\r\n";
$result .= "<html>\r\n<head>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n";
$result .= "<title>Photo Slideshow - easylanweb Intranet</title>\r\n<meta name=\"date\" content=\"".date('r')."\">\r\n";
$result .= "<meta http-equiv=\"content-language\" content=\"en\">\r\n<meta name=\"author\" content=\"Willtech\">\r\n";
$result .= "<link href=\"../css/master.css\" rel=\"stylesheet\" type=\"text/css\">\r\n";
$result .= "<script src=\"../includes/DSM_DynamicSiteMenu.js\" type=\"text/javascript\"></script>\r\n</head>\r\n\r\n";
$result .= "<body>\r\n<SCRIPT TYPE=\"text/javascript\">\r\n<!--\r\nif (top == self)\r\ntop.location='../';\r\n//-->\r\n";
$result .= "</SCRIPT>\r\n<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n<tr>\r\n";
$result .= "<td align=\"center\" valign=\"middle\"><object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"770\" height=\"577\">\r\n";
$result .= "<param name=\"flash_component\" value=\"ImageViewer.swc\">\r\n<param name=\"movie\" value=\"../includes/slide.swf\">\r\n<param name=\"quality\" value=\"high\">\r\n";
$result .= "<param name=\"FlashVars\" value=\"flashlet={imageLinkTarget:'_blank',captionFont:'Verdana',titleFont:'Verdana',showControls:true,frameShow:false,slideDelay:7,captionSize:12,captionColor:#FEFEFE,titleSize:14,transitionsType:'Random',titleColor:#5482A7,slideAutoPlay:true,imageURLs:[";
$result .= $imageURLs;
$result .= "],slideLoop:true,frameThickness:2,imageLinks:[],frameColor:#5482A7,bgColor:#5482A7,imageCaptions:[";
$result .= $imageCaptions;
$result .= "],title:'";
$result .= $title;
$result .= "'}\">\r\n<param name=\"BGCOLOR\" value=\"#5482A7\">\r\n";
$result .= "<embed src=\"../includes/slide.swf\" width=\"770\" height=\"577\" quality=\"high\" flashvars=\"flashlet={imageLinkTarget:'_blank',captionFont:'Verdana',titleFont:'Verdana',showControls:true,frameShow:false,slideDelay:7,captionSize:12,captionColor:#FEFEFE,titleSize:14,transitionsType:'Random',titleColor:#5482A7,slideAutoPlay:true,imageURLs:[";
$result .= $imageURLs;
$result .= "],slideLoop:true,frameThickness:2,imageLinks:[],frameColor:#5482A7,bgColor:#5482A7,imageCaptions:[";
$result .= $imageCaptions;
$result .= "],title:'";
$result .= $title;
$result .= "'}\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" bgcolor=\"#5482A7\"> </embed>\r\n";
$result .= "</object></td>\r\n</tr>\r\n</table>\r\n";
$result .= $emb;
$result .= "\r\n</body>\r\n</html>\r\n";
$result .= "<!-- CACHE:Saved ".date('r')." -->\r\n";
		acmeCache::save($full_url, $result);
	} else {
$result .= "<!-- CACHE:Read  ".date('r')." -->\r\n";
	}
	
	echo($result);

?>
