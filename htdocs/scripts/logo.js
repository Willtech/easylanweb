// JavaScript Document
var MAX_c49b32a9 = '';
MAX_c49b32a9 += "<"+"div id=\"MAX_c49b32a9\" style=\"position:absolute; width:94px; height:37px; z-index:99; left: 0px; top: 0px; visibility: hidden\">\n";
MAX_c49b32a9 += "<"+"table cellspacing=\"0\" cellpadding=\"0\">\n";
MAX_c49b32a9 += "<"+"tr>\n";
MAX_c49b32a9 += "<"+"td  bgcolor=\"#FFFFFF\" align=\"center\">\n";
MAX_c49b32a9 += "<"+"table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
MAX_c49b32a9 += "<"+"tr>\n";
MAX_c49b32a9 += "<"+"td width=\"88\" height=\"31\" align=\"center\" valign=\"middle\" style=\"padding: 2px\"><"+"a href=\'http://www.willtech.com.au/\' target=\'_blank\' onmouseover=\"self.status=\'Willtech\'; return true;\" onmouseout=\"self.status=\'\'; return true;\"><"+"img src=\'/images/willtech.png\' width=\'88\' height=\'31\' alt=\'Willtech\' title=\'Willtech\' border=\'0\' /><"+"/a><"+"/td>\n";
MAX_c49b32a9 += "<"+"/tr>\n";
MAX_c49b32a9 += "<"+"/table>\n";
MAX_c49b32a9 += "<"+"/td>\n";
MAX_c49b32a9 += "<"+"/tr>\n";
MAX_c49b32a9 += "<"+"/table>\n";
MAX_c49b32a9 += "<"+"/div>\n";
document.write(MAX_c49b32a9);

function MAX_findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
  d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i>d.layers.length;i++) x=MAX_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MAX_getClientSize() {
  if (window.innerHeight >= 0) {
    return [window.innerWidth, window.innerHeight];
  } else if (document.documentElement && document.documentElement.clientWidth > 0) {
    return [document.documentElement.clientWidth,document.documentElement.clientHeight]
  } else if (document.body.clientHeight > 0) {
    return [document.body.clientWidth,document.body.clientHeight]
  } else {
    return [0, 0]
  }
}

function MAX_adlayers_place_c49b32a9()
{
  var c = MAX_findObj('MAX_c49b32a9');

  if (!c)
    return false;

  _s='style'

  var clientSize = MAX_getClientSize()
  ih = clientSize[1]
  iw = clientSize[0]

  if(document.all && !window.opera)
  {
    sl = document.body.scrollLeft || document.documentElement.scrollLeft;
    st = document.body.scrollTop || document.documentElement.scrollTop;
    of = 0;
  }
  else
  {
    sl = window.pageXOffset;
    st = window.pageYOffset;

    if (window.opera)
      of = 0;
    else
      of = 16;
  }

     c[_s].left = parseInt(sl+0) + (window.opera?'':'px');
     c[_s].top = parseInt(st+ih - 37) + (window.opera?'':'px');

  c[_s].visibility = MAX_adlayers_visible_c49b32a9;
}


function MAX_simplepop_c49b32a9(what)
{
  var c = MAX_findObj('MAX_c49b32a9');

  if (!c)
    return false;

  if (c.style)
    c = c.style;

  switch(what)
  {
    case 'close':
      MAX_adlayers_visible_c49b32a9 = 'hidden';
      MAX_adlayers_place_c49b32a9();
      window.clearInterval(MAX_adlayers_timerid_c49b32a9);
      break;

    case 'open':
      MAX_adlayers_visible_c49b32a9 = 'visible';
      MAX_adlayers_place_c49b32a9();
      MAX_adlayers_timerid_c49b32a9 = window.setInterval('MAX_adlayers_place_c49b32a9()', 10);

      return window.setTimeout('MAX_simplepop_c49b32a9(\'close\')', 14000);
      break;
  }
}


var MAX_adlayers_timerid_c49b32a9;
var MAX_adlayers_visible_c49b32a9;


MAX_simplepop_c49b32a9('open');