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

/* Willtech list file/dir script */
/* Last Updated at 2008-07-30*/

function listdir ($path) {
	if ($handle = opendir("../content$path")) {
		$countdir = 0;
		while (false !== ($file = readdir($handle))) {
			if (($file !== ".")&&($file !== "..")) {
				$dName = "../content$path$file";
				if (is_dir($dName)) {
					$dirname[$countdir]=$file;
					$countdir++;
				}
			}
		}
		closedir($handle);
	}
	if ($countdir != 0) {
		$array_lowercase = array_map('strtolower', $dirname);
		array_multisort($array_lowercase, SORT_ASC, SORT_STRING, $dirname);
	}
	return $dirname;
}

function listfile ($path) {
	if ($handle = opendir("../content$path")) {
		$countfile = 0;
		while (false !== ($file = readdir($handle))) {
			if (($file != ".")&&($file != "..")) {
				$dName = "../content$path$file";
				if (false == (is_dir($dName))) {
					$filename[$countfile]=$file;
					$countfile++;
				}
			}
		}
		closedir($handle);
	}
	if ($countfile != 0) {
		$fp1 = 0;
		while ($fp1 < $countfile) {
			$firstpart = explode(".", $filename[$fp1]);
			$secondpart[$fp1] = trim($firstpart[0]);
			$fp1++;
		}
		$array_lowercase = array_map('strtolower', $secondpart);
		array_multisort($array_lowercase, SORT_ASC, SORT_STRING, $filename);
	}
	return $filename;
}

function dirlink ($dir) {
	$result = "";
	$testdir = listdir("/$dir/");
	if ( count($testdir) == 0 ) {
		$testfile = listfile("/$dir/");
		if ( count($testfile) == 1) {
			$testname = explode(".", $testfile[0]);
			switch ($testname[1]) {
				case "txt":
    				$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
   				break;
				case "htm":
    				$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
   				break;
				case "html":
    				$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
   				break;
				case "mht":
    				$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
   				break;
				case "jpg":
					$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
				break;
				case "JPG":
					$result = 'href="body.php?go=/'.$dir.'/'.$testfile[0].'" target="bodyFrame"';
				break;
				default:
					$result = "";
				break;
			}
			
		} else {
			$ptpos = 0;
			$slide = 0;
			$aud = 0;
			while ($ptpos < count($testfile)) {
				$testname = explode(".", $testfile[$ptpos]);
				if (($testname[1] == "jpg") or ($testname[1] == "JPG")) {
					$slide++;
				}
				if ((($testname[1] == "wav") OR ($testname[1] == "mid") OR ($testname[1] == "mp3")) && $ptpos !== 0) {
					$slide++;
					$aud++;
				}
				$ptpos++;
			}
			if (($slide && ($slide == count($testfile))) && $aud < 2) {
				$result = 'href="slideshow.php?go=/'.$dir.'/" target="bodyFrame"';
			}
			
		}
		
	}
	
	return $result;
}

function filelink ($file) {
	$result = "";
	$testname = explode(".", $file);
		switch ($testname[1]) {
			case "txt":
				$result = 'href="body.php?go=/'.$file.'" target="bodyFrame"';
			break;
			case "htm":
				$result = 'href="/content/'.$file.'" target="bodyFrame"';
			break;
			case "html":
				$result = 'href="/content/'.$file.'" target="bodyFrame"';
			break;
			case "mht":
				$result = 'href="/content/'.$file.'" target="bodyFrame"';
			break;
			case "url":
				$link = parseurl($file);
				$result = 'href="'.$link.'" target="_blank"';
			break;
			case "jpg":
				$result = 'href="body.php?go=/'.$file.'" target="bodyFrame"';
			break;
			case "JPG":
				$result = 'href="body.php?go=/'.$file.'" target="bodyFrame"';
			break;
			case "gif":
				$result = 'href="body.php?go=/'.$file.'" target="bodyFrame"';
			break;
			case "png":
				$result = 'href="body.php?go=/'.$file.'" target="bodyFrame"';
			break;
			default:
				$result = "";
			break;
		}
	return $result;
}

function grabfile ($file) {
	$handle = fopen("../content/$file", "r");
	$text = fread($handle, filesize("../content/$file"));
	$result = $text;
	fclose($handle);
	return $result;
}

function parsefile ($file) {
	$handle = fopen("../content/$file", "r");
	$text = fread($handle, filesize("../content/$file"));
	$result = preg_replace("/(\n)+/m","<br>",$text);
	fclose($handle);
	return $result;
}

function parseurl ($file) {
	$handle = fopen("../content/$file", "r");
	while (!feof($handle) && ($buffer3[0] !== "URL")) {
		$buffer1 = fgets($handle);
		$buffer2 = explode("://", $buffer1);
		$buffer3 = explode("=http", $buffer2[0]);
	}
	$buffer4 = explode("URL=", $buffer1);
	$result = preg_replace("/(\n)+/m","",$buffer4[1]);
	fclose($handle);
	return $result;
}


function nicesize ($lnktxt, $spot) {
	
	$numch = strlen($lnktxt);
	if (($numch + ($spot * 2)) > 23 ) {
		$result = '<span style="font-size:'.floor(14 * (24 / ($numch + ($spot * 4)))).'px;">'.$lnktxt.'</span>';
	} else {
		$result = $lnktxt;
	}

	return $result;

}

function dozooglelink ($file) {

	$result = "";
	$full = substr($file, strpos($file, "../content/") + strlen("../content/"));
	$testname = explode("/", $full);
	$testpos = count($testname)-1;
	$testext = explode(".", $testname[$testpos]);
		switch ($testext[1]) {
			case "txt":
				$result = 'body.php?go=/'.$full.'" target="bodyFrame';
			break;
			case "htm":
				$result = '../content/'.$full.'" target="bodyFrame';
			break;
			case "html":
				$result = '../content/'.$full.'" target="bodyFrame';
			break;
			case "mht":
				$result = '../content/'.$full.'" target="bodyFrame';
			break;
			case "url":
				$link = parseurl($full);
				$result = $link.'" target="_blank';
			break;
			case "jpg":
				$result = 'body.php?go=/'.$full.'" target="bodyFrame';
			break;
			case "JPG":
				$result = 'body.php?go=/'.$full.'" target="bodyFrame';
			break;
			case "gif":
				$result = 'body.php?go=/'.$full.'" target="bodyFrame';
			break;
			case "png":
				$result = 'body.php?go=/'.$full.'" target="bodyFrame';
			break;
			default:
				$result = $file.'" target="_blank';
			break;
		}
		
	return $result;
}


?>