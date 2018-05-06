/*
	the windoid 
	
	designed for pop-up windows which are full-sized versions of a thumbnail
	see below for thuimbnail code example..
	
	;o)
	(or

	© 2003-> (or @ corz.org ;o)

*/

function OpenWindow(theURL, width, height, left, top) {
	{ 
	window.open(theURL , "" ,"width="+ width +",height="+ height +",left="+ left +", top="+ top +", toolbar=no,directories=0,menubar=no,status=no,resizable=0,location=0,scrollbars=2,copyhistory=0") 
	}
}
function OpenChatWindow(theURL, width, height, left, top) {
	{ 
	window.open(theURL , "" ,"width="+ width +",height="+ height +",left="+ left +", top="+ top +", toolbar=no,directories=0,menubar=no,status=no,resizable=1,location=0,scrollbars=2,copyhistory=1") 
	}
}

function OpenImageWindow(theURL, width, height, left, top) {
	{ 
	window.open("/inc/img/png_holder.php?image="+theURL , "" ,"width="+ (width + 120) +",height="+ (height+110) +",left="+ left +", top="+ top +", toolbar=no,directories=0,menubar=no,status=no,resizable=1,location=0,scrollbars=2,copyhistory=1") 
	}
}

/* feed this function something like this..

<a  class="slice-link" href="img/mfw_small.png" onclick="javascript:OpenImageWindow(this.href,360,330,200,100); return false;" 
	id="Mathematics-for-Women" title="Mathematics for Women, a mandala by (or - opens in a new window"></a>

or..

<script type="text/javascript">
//<![CDATA[
<!--
document.write("<a href=\"javascript:OpenWindow('img/installcap01.jpg',540,260,100,50)\" title=\"all the double-clickness you could want!\nopens in a windoid\"><img src=\"img/installcap01_tn.jpg\" alt=\"all the double-clickness you could want in a unix app!\" \/><\/a>");
//-->
//]]>
</script>
<noscript>
	<a href="img/installcap01.jpg" onclick="window.open(this.href); return false;" title="opens in a new window">
		<img src="img/installcap01_tn.jpg" alt="all the double-clickness you could want in a unix app! />
	</a>
</noscript>

*/