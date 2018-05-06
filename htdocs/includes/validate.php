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

/* Willtech validate script */
/* Last Updated at 2008-07-19*/

function valemail($varname) {
	preg_match_all ("/[a-zA-Z0-9\_\-\.\+\@]*/", $_POST[$varname], $val);
	$clean = "";
	for ($i=0; $i < sizeof ($val[0]); $i++) {
			$clean .= $val[0][$i];
	}
	return $clean;
}

function valtext($varname) {
	preg_match_all ("/[a-zA-Z0-9._@ <>,$()?%+-]*/", $_POST[$varname], $val);
	$clean = "";
	for ($i=0; $i < sizeof ($val[0]); $i++) {
			$clean .= $val[0][$i];
	}
	return htmlentities($clean);
}

function valphone($varname) {
	preg_match_all ("/[0-9 +-]*/", $_POST[$varname], $val);
	$clean = "";
	for ($i=0; $i < sizeof ($val[0]); $i++) {
			$clean .= $val[0][$i];
	}
	return $clean;
}

function valnumb($varname) {
	preg_match_all ("/[0-9 +-]*/", $_POST[$varname], $val);
	$clean = "";
	for ($i=0; $i < sizeof ($val[0]); $i++) {
			$clean .= $val[0][$i];
	}
	return $clean;
}

function valpath($varname) {
	return $clean;
}

function valother($varname) {
	return htmlentities($_POST[$varname]);
}

?>