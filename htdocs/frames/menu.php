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
include_once('../includes/cache-kit.php');
	$cache_active = true;
	$cache_folder = 'cache/';
	$cache_time = 600; // 31536000 a year, 2628000 a month, 606462 a week, 86400 a day, 60 = 1 minute 
	
	$site_url = $_SERVER["HTTP_HOST"];
	$full_url = "http://".$site_url.$_SERVER["REQUEST_URI"];

	$result = acmeCache::fetch($full_url, $cache_time);
	if(!$result){
	$result  = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\r\n";
	$result .= "<html>\r\n<head>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n";
	$result .= "<title>Left Menu - easylanweb Intranet</title>\r\n<meta name=\"date\" content=\"".date('r')."\">\r\n";
	$result .= "<meta http-equiv=\"content-language\" content=\"en\">\r\n<meta name=\"author\" content=\"Willtech\">\r\n";
	$result .= "<link href=\"../css/master.css\" rel=\"stylesheet\" type=\"text/css\">\r\n</head>\r\n\r\n<body class=\"menu\">\r\n";
	$result .= "<SCRIPT TYPE=\"text/javascript\">\r\n<!--\r\nif (top == self)\r\n   top.location='../';\r\n//-->\r\n</SCRIPT>\r\n";
	$result .= "<br>\r\n<a href=\"/\" target=\"_top\" class=\"boldlink\" style=\"padding-left:39px;\">Home</a>\r\n<br>\r\n";
$hpos = 0;
$vpos = 0;
$go = explode("/", $_GET['go']);
$hmax = count($go);
$dirlist = listdir("/");
$vmax = count($dirlist);
while ($vpos < $vmax) {
	if ($dirlist[$vpos] == $go[$hpos]) {
		$tag = nicesize ($dirlist[$vpos], $hpos);
	$result .= '<a href="menu.php" target="menuFrame" class="boldlink"><img src="../images/Icons/folder_add_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
		$dirpath = $go[$hpos];
		$hpos++;
		while ($hpos < $hmax) {
			$hcount=0;
			while ($hcount < $hpos+1) {
	$result .= "&nbsp;&nbsp;";
				$hcount++;
			}
	$result .= '<a href="menu.php?go=';
			$hcount=$hpos;
			while ($hcount < $hpos+1) {
				$dirpath .= "/".$go[$hcount];
				$hcount++;
			}
			$upcntr = 1;
			$uppath = $go[$upcntr-1];
			$upcntr ++;
			while ($upcntr < $hpos+1) {
				$uppath .= "/".$go[$upcntr-1];
				$upcntr ++;
			}
	$result .= $uppath;
			$tag = nicesize ($go[$hcount-1], $hpos);
	$result .= '" target="menuFrame"><img src="../images/Icons/folder_add_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
			$hpos++;
		}
		$indir = listdir("/$dirpath/");
		$incountd = count($indir);
		$inplace = 0;
		while ($inplace < $incountd) {
			$hcount=0;
			while ($hcount < $hpos+1) {
	$result .= "&nbsp;&nbsp;";
				$hcount++;
			}
			$dirtype = dirlink("$dirpath/$indir[$inplace]");
			$tag = nicesize ($indir[$inplace], $hpos);
			if ($dirtype == "") {
	$result .= '<a href="menu.php?go='."$dirpath/$indir[$inplace]".'" target="menuFrame"><img src="../images/Icons/folder_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
			} else {
	$result .= '<a '.$dirtype.'><img src="../images/Icons/folder_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
			}
			$inplace++;
		}
		$infile = listfile("/$dirpath/");
		$incountf = count($infile);
		if ($incountd == 0 && $incountf == 0) {
			$hcount=0;
			while ($hcount < $hpos) {
	$result .= "&nbsp;&nbsp;";
				$hcount++;
			}
	$result .= '-- empty --<br>'."\r\n";
		}
		$inplace = 0;
		while ($inplace < $incountf) {
			$hcount=0;
			$fullpath = "$dirpath/$infile[$inplace]";
			$title = explode(".", $infile[$inplace]);
			while ($hcount < $hpos+1) {
	$result .= "&nbsp;&nbsp;";
				$hcount++;
			}
			$filetype = filelink($fullpath);
			$tag = nicesize ($title[0], $hpos);
			if ($filetype == "") {
	$result .= '<a href="../content/'.$fullpath.'"  target="_blank"><img src="../images/Icons/activity_window_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
			} else {
	$result .= '<a '.$filetype.'><img src="../images/Icons/activity_window_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
			}
			$inplace++;
		}
	} else {
		$dirtype = dirlink($dirlist[$vpos]);
		if ($dirtype == "") {
			$tag = nicesize ($dirlist[$vpos], 0);
	$result .= '<a href="menu.php?go='.$dirlist[$vpos].'" target="menuFrame" class="boldlink"><img src="../images/Icons/folder_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
		} else {
			$tag = nicesize ($dirlist[$vpos], 0);
	$result .= '<a '.$dirtype.' class="boldlink"><img src="../images/Icons/folder_32.png" width="32" height="32" align="absmiddle" class="menuimage">'.$tag.'</a><br>'."\r\n";
		}
	}
	$vpos++;
}
//<a href="corzoogle.php" target="bodyFrame" class="boldlink" style="padding-left:39px">Search</a>
	$result .= "<form method=\"get\" action=\"/frames/corzoogle.php\" target=\"bodyFrame\" name=\"corzoogle\" style=\"padding-left:39px; padding-right: 0px; padding-top: 0px; padding-bottom: 0px;\">\r\n";
	$result .= "<input type=\"text\" name=\"q\" size=\"18\" maxlength=\"256\" value=\"search\" title=\"you can narrow your search by using multiple terms,\r\nalso use -words to NOT search for particular words click the 'tips' link for more info\" style=\"margin:0px; padding:0px;\" onClick=\"this.value='';\">\r\n<br>\r\n";
	$result .= "<input type=\"submit\" value=\"Search!\" title=\"corzoogle locates!\" style=\"margin:0px; padding:0px; display:none\">\r\n</form>\r\n<br>\r\n";
	$result .= "<script type='text/javascript' src='/scripts/logo.js?zoneid=3585&source=GitHub&target=_blank&cb=1224213899&layerstyle=simple&align=left&valign=bottom&padding=2&closetime=14&padding=2&shifth=0&shiftv=0&closebutton=f&backcolor=FFFFFF&noborder=t'></script>\r\n</body>\r\n</html>\r\n";
	$result .= "<!-- CACHE:Saved ".date('r')." -->\r\n";
		acmeCache::save($full_url, $result);
	} else {
		$result .= "<!-- CACHE:Read  ".date('r')." -->\r\n";
	}
	
	echo($result);

?>