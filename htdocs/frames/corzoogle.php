<?php if (isset($_GET["zoogleimg"])) { zoogleimg(); die(); }
$version = '0.8rc2'; // for php >= v4.2

include('../includes/functions.php');
include_once('../includes/cache-kit.php');
	$cache_active = true;
	$cache_folder = 'cache/';
	$cache_time = 86400; // 31536000 a year, 2628000 a month, 606462 a week, 86400 a day, 60 = 1 minute 
	
	$site_url = $_SERVER["HTTP_HOST"];
	$cache_url = "http://".$site_url.$_SERVER["REQUEST_URI"];


/*
		  :!: by using this software you agree to the terms of the license :!:
								  (at the bottom)

		 			   © (or @ corz.org & corzoogle.com 2004 ->

								welcome to corzoogle!

			..the realtime search engine for humans and their documents..


	  :!: utf-8 (no bom); unix linefeeds; text: monaco/profont 9pt, 4 spaces/tab :!:
		 :!: this file does _not_ need to be world-writable (chmod 755-ish) :!:

	-v- IMPORTANT -v-  -v- 		  -v- FOR WEB SITES -v-  -v-		-v- IMPORTANT -v-

		this software performs realtime searches on the raw data of your filesystem.
		unlike other search systems which "index" content on a daily/weekly/manually
		basis, corzoogle searches in realtime; LIVE. If a file is altered and you
		search, it will appear in the results immediately. while this live raw
		searching is good, you don't want it to happen to a file with passwords in
		it. corzoogle comes with THREE ways to protect your passwords. firstly whole
		folders can be excluded..

		/includes/ are generally excluded, ironically

		you can also exclude specific named files from the search, "config.php"
		files spring to mind. Lastly, including the word "nosearch" *anywhere* in
		any file (a standard comment is ideal) and that file will NOT be searched.

		All these options are completely configurable in the preferences section
		below. Note there is now an option to *not* search within php/html tags.

	-^- IMPORTANT -^-  -^- 		  -^- FOR WEB SITES -^-  -^-	   -^-  IMPORTANT -^-

		if you're using corzoogle to search your own personal hard drive, you can
		probably forget all this stuff and just drop this very file into whatever
		folder you want to (recursively **) search. of course you'll need to have a
		webserver running to use this. [I kneel in the church of..] Apache is
		installed as standard on Mac OS X, most Linux/*nix/BSD distros, and also
		real easy to install on Windows boxes too, unless you're ronnie heh.

		remember, the smaller the area corzoogle has to cover, the quicker the
		search will be, so if you are using corzoogle to search HUGE (20,000+ docu-
		ments) archives, you might want to copy corzoogles into the subfolders for
		rapid local searches, I've got them all over the place.

		any web server that can run php should have no trouble. even [*tssssss*]
		IIs, I guess. it's "built" into most XP's & win2K's. untested, syat. Apache
		runs great on windows, and the Apache team have done a great job with their
		Windows binary installers. 
		
		with a webserver running on your desktop machine, corzoogle doubles up
		fairly easily as a rapid realtime 'desktop' search engine.

		feel free to mail me about anything corzoogle-related, putting 'corzoogle'
		somewhere in the subject header appeases my shitlist daemon.

		hey! it's cz! so have fun!

		;o)
		(or

		** .. and all the folders inside it, and inside them, and so on.

		ps.. hoping you might find customisation desirable, I've designed and coded
		corzoogle considering  readablility over cleverness, although there are a
		couple of clever bits, too.

		If you are new to php, maybe even if you're not, there's probably something
		of value in my copious commantage. some gags, too. most importantly, the
		routines are designed for speed. if something ugly is faster than something
		cute, we go with ugly. with a built-in timer, it's easy enough to test even
		minute performance variations. if you see something slow, let me know!

		final note: error_reporting is now OFF by default. this saves us some
		potential needless headaches, but also, of course, means that if for some
		reason you can't get corzoogle working, you won't know why! it's unlikely,
		but if you ever need to enable error reporting, search for :errors: in this
		file and uncomment that line. Then MAIL ME the error!	*/


/*
preferences..	*/


/*
	security..


	the VERY important IGNORE folders. their contents will NOT be searched.

	security aside, this is useful for avoiding image galleries, smilie folders,
	etcetera. but you may wish to search for filenames in these places, so it's up
	to you..	*/
$ignore = array(
	'/err/',
	'/inc/',
	'/includes/',
	'/cgi-bin/',
	'/private/'
	);

/*		you only need to enter one instance of '/inc/', (for example)
		to cover every 'inc' folder in your entire website.

		IMPORTANT: you *must* precede the directory with a slash "/".
		if you don't, you will loop into infinity. also remember to
		*remove* from the list, any items you don't need.	*/

/*
	filenames to NEVER search inside, anywhere, ever.
*/
$private = 'config.php,robots.txt,links.php'; // 		<-------‹‹	!!:!:!! IMPORTANT !!:!:!!

/*	this is especially for files which would normally be *allowed* by
	their $extensions, like config.php files	*/


//	:!:	note: invisible files (beginning with a dot '.') are NEVER searched :!:


/*
	putting "nosearch" (no quotes) anywhere inside a file means it will NOT be scanned	*/
$dont_search = 'nosearch'; /* or some other word or phrase of your choosing.

	a simple html comment..  <!-- nosearch -->	..is ideal. CaSe must mach exactly.

	obviously, the file you are reading now will never appear in your search results.

	of course, in reality, most all of these files are searched otherwise,
	how would we know which ones to *display*?


miscellaneous preferences..	*/


/*	utf-8

	unicode rocks!
	I'm moving away from .htaccess tweaks now..
	if you have all-windows, all-american or something, you could comment this out	*/
$use_utf8 = true;	//	(true/false) default: $use_utf8 = true;



/*	extensions..
	[need] default: $extentions = '..html.txt.doc.phps.blog.comment';

	file types to search inside - basic, but effective.
	note extensions within extensions will pass, so .html covers .htm files.
	do leave the extra dot '.' at the start. faster than an array.
*/
$extentions = '..html.txt.doc.phps.blog.comment';/*
.php files covered here ----›^^^^‹----- etc.


/*
	extension mangling

	let's say you have a php site, and you keep your content in files
	anding .htm, but if they are accessed directly, you redirect
	(with htaccess) to the php container page. now you can have corzoogle
	alter the extension for you, so the link goes directly to the
	php file.

	another use is for file.php.comment files, rather than the raw comment
	file, you want the user to load the page it is attached to.
	uncomment to use this feature..											*/
//$mangle = array( '.htm'=>'.php' , '.php.comment'=>'.php#comments' );

// note the double extension on the second entry. handy.
// also note, mangling doesn't operate on your ".blog" files, if you use that.


/*
	searching will stop after this many results.
	default: $max_hits = 200;
*/
$max_hits = 200;
/*
	perhaps I will add a message along the lines of..
	"consider narrowing your search by using more search terms"


/*	search between..

	[optional] default:	$search_between = array('<body','</body>');

	by default, corzoogle will search for content anywhere between the <body> and </body> tags.
	you can alter this behaviour here, perhaps broadening searches to <html></html> tags, or
	whatever you like. you could even make up your own <start-search><end-search> tags. using the
	<html></html> tags will always get you more hits, generally, but will degrades the quality of
	the results both at the search and result presentation stages.

	This works independently of title scoring and descriptions. note: no closing '>' on the
	first entry. this is important, particularly for body tags.

	if a file doesn't have this tag (and it's allowed by its extension, like a plain text file),
	then the whole file is searched.	comment out to disable search_between altogether.	*/
$search_between = array('<body','</body>');

//	note: you can use mixed tags..	$search_between = array('<tr','</body>'); for instance

/*
	this means "Search Between Tag is Case Insensitive". it's around 3% slower, but if you
	have a mix of case in the html tags of your documents (*tsss*) you can enable this	*/
$sbtici = true;	// (true/false)	default: $sbtici = false;

/*	you can leave the above at "false" generally, if you are searching between <body></body> tags
	and some document has <BODY></BODY> tags instead, corzoogle will just search the whole thing	*/

/*
	or you can tell corzoogle to ONLY search documents that contain your $search_between tags.
	enable this only if you understand the implications, particularly if you (like me) split
	you page structures and body content into separate files, the one with the <body> tags
	has no content! this is highly useful if you utilise custom search_between tags, or want to
	limit your searching to only particular kinds of content, however. 	*/
$enforce_between = false;	// (true/false)	default: $enforce_between = false;


/*
	a note about how disabling search_between can alter results..

	contrary to what one might imagine, disabling search_between can wildly alter the quality and
	quantity of your results. it all depends on how your pages are designed, and wether or not you
	enable $search_in_tags (further down the prefs). when you disable $search_between, corzoogle
	always scans the whole document.

	consider this.. you have a php document, the whole page is contained within it, the html being
	output by "echo" commands. if you are searching between <body> tags, corzoogle will find those
	and do as you expect. if, however, you disable $search_between, you are asking corzoogle to
	scan the whole document. as the whole document is inside one big <?php tag, it will be ignored
	in its entirety.

	outputing whole documents with echo commands isn't very clever, but still, folks do it. they'd
	be better served moving the html to another file and "including" it. easier to edit too. armed
	with this information you can better tweak your searching. also consider custom $search_between
	tags.	*/


/*	show recent searches..

	(true/false)	default:	$recent_hits = true;

	whether to allow "most recent searches" at the foot of the results page. this is fun,
	you can see what folks have been searching for. creates live links, the unfollowed ones
	being "other people's" searches. interesting data. I might make it "random recent searches"
*/
$recent_hits = false;
/*
	:!: to do this, corzoogle creates a file called ".corzoogles" in the same folder as this
	file. If the folder doesn't have write access (likely), you will get an error. The error
	message will guide you into the solution. Probably the best method for "live" sites would
	be to create the file locally (by simply corzoogling once) and then upload the ".corzoogles"
	file to your website, chmod it to 777 (or use FTP client to set its write permissions to ALL)	*/

/*
	length of recent hits
	we only want to show 'so many' past hits so there's no wrapping
	default: $hits_length = 81;	(enough hits to fill 81 characters)	*/
$hits_length = 81;



/*	use preview snippets..
	[optional: true/false] default: $use_snippets = true;

	corzoogle grabs previews from the body of the text, one for each search term, and strings
	them all together to create a nice preview snippet. this is good.	*/
$use_snippets = true;

/*	the total length of the preview can be configured here. the default is 300. small is best.

	this remains constant regardless of the number of query terms; corzoogle will grab a small
	chunk of text from the query term onwards. if there were five words in the query, it will
	create five snippets, each ( $snippet_length / 5 ) characters long and join them together.	*/
$snippet_length = 300;


/*	use descriptions in the result text..
	[optional: true/false] default: $use_descriptions = true;

	if a <description> meta tag exists in the document, we can optionally instruct corzoogle
	to display it directly between the title and the preview snippet of each result.	*/
$use_descriptions = true;


/*	add fairly ugly highlights to exact case-matched words in the preview..
	[true/false] default: $highlight_exact_hits = false;

	If you searched for BigBob, then "bigbob" and "BIGBOB" are still hits, of course,
	and get their usual coloring in the preview, but "BigBob" gets a yellow highlighter-
	pen type mark through it.	*eew*	*/
$highlight_exact_hits = true;


/*
	preview has "context"
	default: $preview_has_context = true;

	Normally we return the preview snippet starting directly at the query term. In a search for
	"mac", the preview might begin.. "mac news and information. this is the most up-to-date...".

	If we set this to true, the preview snippet begins *before* the query, and the query is placed
	some way into the snippet, so now the same result might read.. "click here for the latest mac
	news and information. this is the most up-to-date..." which gives the result some
	context, aids clarity.
*/
$preview_has_context = true;

/*
	by default, corzoogle presents the query term one quarter of the way into the snippet.
	this is expressed as 1/4, so the context ratio is 4. If you'd like the term to appear earlier
	in the snippet use 5 or 6. To have the term appear exactly half way through the snippet, use 2.

	because of the law of diminishing thingamybobs, it is impossible to get the query term to appear
	right at the start by using some HUGE number**, just make $preview_has_context = false!
*/
$context_ratio = 4;

//	** well, maybe a REALLY BIG one!


/*	filename searching..
	[optional] default: $do_filenames = true;

		corzoogle can return matches for filenames, too. users only need to include a word
		with a dot "." to invoke this filename-searching behaviour.

		searching for..

	download.php

		would return a list of all documents containing the term "download.php" as usual,
		but also an additional list of *files* matching "download.php".

	secure index.php

		would return documents containing the words "secure" AND "download.php", and the
		very same list of matching files as our last query. most likely successful file-name
		searching will be best achieved with single terms..

	security.txt

		you can use wildcard file matching too. a search for..

	.html

		will return a list of all files matching .html, in other words, all html files in
		corzoole's search zone, or any file that contains the term "html" in its name. it
		works the other way too, a search for..

	index.

		will return a list of all files with "index" anywhere in their filename. searching for..

	.index

		achieves exactly the same file search result, though probably with a great deal less
		regular page hits above it. this is useful when searching *only* for matching
		filenames.

		to invoke this file searching behaviour, all you do is add a dot!

		this directive also affects the +10 bonus that hits get for matching each query string
		in their file name, which is a nice touch. a search for "security" would match all
		documents containing that word, but push those with the words "security" *anywhere*
		in their file name a little closer to the top of the pile. 3 matching terms in the
		filename would be +30, etc. you can set the bonus here too.
*/
$do_filenames = true;
$filename_bonus = 10;

/*
scoring prefs (ranking) ..

		corzoogle ranks hits by the frequency of search terms contained within the document if
		the user inputs multiple terms, the search is narrowed down to documents containing ALL
		the terms. In other words, "boolean AND".

		you can use individual boolean NOT's, for instance..

	mac osx sudo box -apple -madness

		searches for words containing "mac" AND "osx" AND "sudo" AND "box", but will NOT
		return any hits for documents containing the word "apple", or the word "madness".


		simple scoring:
		each term scores 3 points per occurrence. in a search for..

	mac osx sudo box

		a document containing nine instances of the word "mac", six instances of the
		word "osx", and three instances each of the words "sudo" and "box, would score..

		(9*3)+(6*3)+(3*3)+(3*3) = 27+15+9+9 = 63

		this simple scoring may be just what you need, but corzoogle's default is to use..

weighted_scoring..
[need] default: $weighted_scoring = true;
*/
$weighted_scoring = true;
/*
		with weighted scoring the earlier words score more heavily, when searching for..

		that same search now scores (9*4)+(6*3)+(3*2)+(3*1) = 36+18+6+3 = 60. not 63. read on..

		"mac"'s score is capped at $q_word_max (default is 33, set below). no one single
		word can score more than $q_word_max points (in either simple or weighted scoring mode)
		if there had been fifty hits for "mac", it still would have scored 33 points.
		so the actual score is 60.

		if someone had searched for the same terms in *this* order..

	sudo box mac osx

		that same document would have scored (3*4)+(3*3)+(9*2)+(3*1) = 12+9+18+3 = 42

		normally the first term scores 4, the second 3, and so on until all terms
		score one point. you can set a higher weight here..
*/
$weight = 4;		//	(integer) default: $weight = 4;

/*
		individual query term scoring continues up to a maximum of this amount..

(integer) default: $q_max_score = 100;		*/
$q_max_score = 100;

/*		regardless of any scoring prefs, exact case-sensitive word matches always scores
		an additional 1 point bonus. this is in addition to max scores.	if two documents
		contain the exact number of matched terms, the one with an exact case-sensitive
		match will always pip the other at the post.	*/

/*
		you can also set the value that one single word can achieve in the results. works
		in simple or weighted mode. default is one third (33%) of $q_max_score. depending
		on your document content, you may want to adjust this, anything in the 20-60%
		works well. 33% is good for general usage. if you are doing a lot of single-word
		queries, or are using a large $weight, you might want to set this a wee bit higher.
*/
$q_word_max = 33;	// (integer) default: $q_word_max = 33;
/*
		which is a simple cap, set to prevent runaway terms from affecting the results too
		much. the examples above were cleverly chosen to produce the same results in both
		modes. nah, it was just a "coincedence".

		although we do check for "stop-words" (common words like "the", "and", etc), in
		reality, users don't input these kinds of words, mostly they are genuinely
		searching for real documents!

		you can choose your own stopwords here, too.
*/
$stop_words = array	('' // some of these are probably not necessary
	,'&nbsp;',';o','a','A','all','and','are','as', 'at','be','but','by','can','do','don\'t'
	,'for','got','have','he','here','I','in','if','is','it','like','me','my','n','no'
	,'o','of','on','one','or','out','she','so','t','than','then','that','that\'s'
	,'the','The','there','there\'s','these','this','to','too','was','we','with','you'
	);
/*
	if any of these stop-words are removed from the search, it will be reported back
	to the user. try a few and you'll see. remember to "escape" (put a backslash
	in front of) all single quotes inside stop-words here, 'won\'t', for instance.
	or else use double-quotes around those ones.. "won't", "can't", etc

/*	I still can't decide which is faster, single or double quotes. my brain says
	single quotes should *always* be faster, as there's less to check for. hmmph.	*/

/*
phrase matching..
*/
$match_phrases = true;	//	(true/false)	default: $match_phrases = true;
/*
	if the original query is a phrase (two words or more), and all the terms exist in a
	document, it is then checked for matching "phrases"

		searching for..

	Wonkey Man Mac

		matches "and I came upon The Wonkey Man Machine and gasped like a silly monkey"

		jackpot! whole query phrase exists *in entirety* inside the document,
		with the exact SaMe cAsE..

		bonus = 20% + (10% * per query term in phrase) = 20% + 30%  = 50%
		exact matching query phrase of three words, this is a good match.

	wonkey man mac

		(a case insensitive match) scores  10% + 5%/term  = 25%

		the longer the matching phrase, the bigger the extra bonus. all these
		scores are relative to..

$phrase_max_score
(integer) default: $phrase_max_score = 50;
*/
$phrase_max_score = 50;
/*
	this scoring element is in *addition* to $q_max_score, and is in itself a
	*relative* score, read on ...
*/


/*
titles scoring..
(true/false) default: $title_scoring = true;


	IMPORTANT:
	unlike query term scoring, these scores are *not* caps. The numbers given in the examples
	below are correct at the default of $title_max_score = 100; If you alter $title_max_score,
	you also alter those values, in other words, title scores are *relative* to whatever maximum
	value you set here.

	This enables us to set a convenient body-to-title ratio, adjusting the level of importance,
	or "weight" of either aspect, depending on what _you_ need. you could set this so even the
	best matching titles add only another 20, or make title searching THE most important factor,
	setting it to 150, 200, or more.


	this is how corzoogle scores a document for its document <title>..


	if the whole query string is a phrase (two words or more) and matches the title
	even in part, (case in-sensitive match), the document scores +20

		if that same query string also matches the entire title *exactly* in a case-
		insensitive manner, a further 60 points are awarded (80 for this title)

		OR

		title and query match *exacitaly*, CaSe AND Length, so it's +80. (maxed!)


	OR (no "phrases" found)..

	the page scores +10 for each query term that's in the title *somewhere*


	in a search for..

		Mac Madness on Mars

	a document with the title "mars madness for your macintosh" would score +30
	a document with the title "The mac madness on Mars" would score +80
	a document with the title "Mac Madness on Mars" would score +100

*/
$title_scoring = true;


/*
you can set the maximum score (weight) a <title> can achieve. default is 100
*/
$title_max_score = 100;


/*	score all titles..
	(true/false) default: $score_all_titles = false;

	by default, corzoogle will only take titles into consideration if there has already
	been a match in the body of the document (or whatever you have set it to $search_between)

	a document's <title> will only be scored if it has already been matched by its content.
	if you are searching between <body> tags, the header part of the document simlply wont
	be scanned for matches unless there has been FULL match somewhere in the body content of
	the document (and that means ALL terms in a multiple query).

	only documents that are hits because of matching <body> content will be scrutinized for
	their title content, then scored and weighted according to its relevance, again giving
	the possibility of more fine-tuned results.

	if you want, you can direct corzoogle to look at ALL titles, regardless of whether
	there was a  body text match or not. if this is set to true, even a single term from a
	multi-term query will trigger a hit. in some searches there will be a small (2-3%)
	performance penalty for this, but if you need to score all titles, here you go..
*/
$score_all_titles = false;


/*
	content as title

	default: $contents_as_title = false;

	under certain circumstances, you may want to have corzoogle use the body of the
	text as the title for the hit, rather than its filename. This only ocmes into effect if
	the document has no "real" title, and you have set $score_all_titles = true; (above)
	
	this may be useful where you have a lot of text files with intelligible titles.
	*/
$contents_as_title = false;


/*
	"content as title" length
	
	title will truncate at this point.
	default: $cat_length = 33;
	*/
$cat_length = 33;



/*
	note: in the absence of a <title> tag, the filename acts as the document's title
*/


/*	show scores
	(true/false) default: $show_scores = false;

	mainly for debugging and tweaking your scoring system.
	fairly ugly		*/
$show_scores = false;



/*	search inside php/html tags?

	you can choose to not search for text inside html/php elements..
	if you are using corzoogle as an onsite "grep", set this to true.
	setting to false will generally speed up searches for words like "bottom"
	but slow down most other searches slightly.

	note: this isn't 100% foolproof. If you have a document with malformed tags
	corzoogle (or rather, the php strip_tags function) will err on the side of
	caution, and search inside it, ignoring it's tagness.	*/
$search_in_tags = false;// (true/false)	default: $search_in_tags = false;



/*	corzblog..

	if you run corzblog (my blogging system) and want your blogs and blog archives to be
	searchable (or at least, have corzoogle return valid links to them) alter this to whatever
	extension you use for your blogs, usually '.blog'	*/
$blog_ext = '.blog';
/*
	corzoogle will now translate a hit such as.. /blog/arc/2003-nov.blog
	into..	/blog/index.php?archive=2003-nov
	you could use this mechanism for other generated pages, too, but you'd
	need to slightly alter the code inside the look_in_file function.	*/

// name of blog archive directory (inside blog folder)..
$arc_name = 'arc';



/*	deepness:
	default: $deepness = false;

	if you are running corzoogle inside a subfolder of the web server, but want to
	search from the dir ABOVE that, you can specify that here. your results will
	be a adjusted accordingly. 	(dir = directory = folder)

	remember: specifying deepness means you search from the dir ABOVE the dir that
	corzoogle.php lives IN THE REAL FILE SYSTEM, symbolic links are followed.
*/
$deepness = true;
/*
	okay, deepness examples..

	1) I have a server.. "soho" (http://soho) .. this is a local webserver. In it I have a copy
	of the php documentation, in a folder called "phpman". I want to search only in the documentation

	$deepness = false;
	corzoogle URL = http://soho/phpman/corzoogle.php

	2) Same server, I have a virtual host called "dev", inside that I have a folder
	called "search". I have renamed corzoogle to "index.php" and dropped it into the
	"search" folder. I want to search from http://dev  (more exciting developer documents)

	$deepness = true;
	corzoogle URL = http://dev/search/

	3) I have a virtual host called "corzorg" (http://corzorg) and I want to search it ALL (hellyeas!)

	$deepness = false;
	corzoogle URL = http://corzorg/corzoogle.php

	hopefully that makes things clear


:!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!:
:!:	    you can drop corzoogle into ANY folder at ANY depth and it will, by default, produce    :!:
:!:     correct results, correct URL's. "deepness" is only needed for searching from ABOVE.     :!:
:!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!:


	just thought I'd do a cute notice box.



/*
	embedded "corzoogle" image		default: $logo = 'embedded';

	lots of folks don't realise that the image is embedded *inside* this script.
	yes, this is funky, and does make corzoogle totally portable. however, if you
	run corzoogle on a live website, remember, the image is generated for *every*
	hit on corzoogle.php, *twice* if it's a long page of results (form at bottom too)

	In other words, using a static image is preferable for live web sites (download
	one from my site, if you like.. http://corz.org/img/corzoogle_sm.png or you can
	save it from any corzoogle page), or use your own.

	this way, the user's web browser cache will keep the image handy, save you some
	bandwidth and cpu. sure, not a lot, but it all mounts up. it will also appear
	quicker to the user. on a local site, this isn't an issue.

	options are 'embedded' or '/real/image/location' something like..

	$logo = '/img/corzoogle_sm.png';
*/
// $logo = 'embedded';
$logo = '/images/corzoogle_sm.png';

// you can also comment out the above line and have no logo at all.


/*
	embedded corzoogle!		(true/false)	default: $embedded = false;

	it's always been easy enough to run corzoogle from inside another page, but now
	it's even easier! just set this to true, and include corzoogle in your page...

include('corzoogle.php');

	remember to enclose in php tags if it isn't already.

	corzoogle will return its results in the same given space. remember to set "deepness"
	if you are in a sub-folder of your site (like.. http://mysite.org/search/), and also
	to use a *real* image for your logo, or else the logo will be disabled. you might
	also want to do some (small) css (a div) for the "#description" text.
	*/
$embedded = false;



/*
	domain preferences..	*/


/*
	this should be fine as is. you might know better.
	used for checking referrer information (for hot-linking)
	and also for building the file-paths of $filename matches..
*/
$domain = $_SERVER['HTTP_HOST'];


/*
	hot-linking. FALSE! FALSE! FALSE!!! okay, maybe true.
	you can even *allow* hot-linking, if you like (crazy person)..
	I'm easing up about this. it's not so bad, you know, folks could search
	your content from other sites.	why not! okay dammit, true it is..	*/
$allow_hot_link = true;

/*
	if you do *not* allow hot-linking, what message will be displayed
	when someone attempts to hot-link? ..	*/
$hot_link_message = 'thanks for droppping by!';


/*	search redirection..
	[optional] default: $redirect = false;

	by default, corzoogle sends the queries to itself, of course.

	optionally, you can have corzoogle redirect your queries to another corzoogle
	engine, even one existing in a different domain or website. (that you own)

	remember, if the receiving corzoogle engine is in a different domain from the
	sending corzoogle, you must allow hot-linking at the receiving end.

*/
$redirect = false;
$redirect_to = 'http://'.$domain; //.'/physics/search/index.php';


/*
	safe level
	this has nothing to do with security		default: $safe_level = 0;

	normally, you wont ever need to touch this setting, but if you are having trouble
	getting corzoogle to work on your site, you could try increasing this number to
	1 or 2 and see if that fixes it. Generally there will be slight performance losses
	for enabling these safety features. At any rate, switch on error reporting and send
	me a copy of any errors you get.	*/
$safe_level = 0;


/*	search zones..
	just thinking about this
*/



/*
	notify on searches

	some one asked for this, a nice idea if you are one of those webmasters who *really cares* about
	their visitors. email the webmaster on every search. let me know if you want more features here
*/
$notify = false;	// (true/false)	default: $notify = false;


/*
	prefs for the notification email you will receive
	you might want to mess about with this.		*/

$to_addy = "me@my.com";
$from_addy = "corzoogle@$domain";
$email_subject = "corzoogler!";
$email_body = "There has been a new corzoogle search!\n";
$xmail_headers = "Reply-To: $from_addy\r\n";
//$xmail_headers = "Organization: $domain\r\n";
// etc..

/*
	remember to use full \r\n linebreaks between (additional) x-headers	*/




/*
    :!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!:
    :!:   you are finished here now, please save this file and corzoogle for "pass".    :!:
    :!:   see if any of your passwords turn up. no really, please try that right now.   :!:
    :!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!::!:


	That's it!

	I hope you enjoy using corzoogle as much as I do, it's fairly changed my working life,
	having all these years of collected infos, manuals and and technical documents *just
	there*, a corzoogle away. It sure made developing this a helluva lot easier, that php
	documentation really is quite excellent.. once you find what you need. It's only my
	second "real" php project, but already I'm mightily impressed with the stuff, sure is
	server- side magic, might just be the thing that brings the power of the web server
	right "into the home", looking forward to that, making wee tools.

	for now..

	;o)
	(or

 /*
 *	end of ordinary user preferences
*/



// smoothness..
ob_start();


/*
okay, let's shake this thing..	*/


if ($embedded != true) { do_header(); } elseif($use_utf8 = true) {
	echo '<meta http-equiv=content-type content="text/html; charset=utf-8">'; }
if ($use_utf8 = true) ini_set('default_charset','utf-8');


/*
start timer..

the old 233MHz linux webserver on my LAN can search 8346 files
spanning the 324 folders in "public_html", 72MB of data in around
9 seconds. searching my corz.org mirror (running on the same server)
takes around 0.8 seconds. on the "real" corz.org webserver that same
seach takes around 0.1 seconds. this is to give you an idea. I've seen
sites with 20,000+ documents being corzoogled (on modern web servers)
in under two seconds. pretty good.
*/
$search_time = explode(' ',microtime());
$start_time = $search_time[1].substr($search_time[0],1);


/*	maximum execution time

	for HUGE sites/archives (GB's), use 60, 120, 180, whatever.
	(we're waiting for the filesystem to do a *full* text search)

	on sites/regular sized archives, you can usually comment this out, generally,
	times will be in the 0.01s-1.0s range, well within php's default of 30 seconds.
*/
//ini_set ('max_execution_time', 180);



error_reporting(0);	// the default for all real production servers.
/*
	report no errors. I made this the default, saves email!

	use this if your server is scanning mounted remote volumes!
	some weird php glitch concerning remote compilation errors can crop up,
	won't affect results at all. I haven't seen this error for ages.

	:!:	on real live webservers, PHP error reporting should ALWAYS be OFF, but
	amazingly, often isn't. You can set this in your scripts, or in your root .htaccess
	file. You'll definitely want to enable error *logging* instead.	*/


// error_reporting(E_ALL);	//:errors: for debugging, uncomment this AND MAIL ME THE OUTPUT!

/*	apologies for shouting, but this is important, all THIS work is for free*, so if your error
	can make this FREE software better, you owe it to me, and to the world, to TELL ME ABOUT IT!
	*ahem* sorry, won't happen again.
*//*
 *sure, I get paid sometimes, but that's not why I do this!	*/

//	hmm..
global $corzoogle, $qc;

//	nice to know that folk are searching for God at my site
if ($recent_hits) $past_hits = array();


/* advanced search mode? (try to avoid this, if possible)
	if(isset($_GET['advanced'])) $advanced = true;	*/


/*	multibyte & unicode support..
	hmm.. have to think more about this. php isn't exactly unicode friendly atm,
	getting there though. will need to be an on/off switch, mb_functions
	are not fast. Some really wacky foreign language sites use corzoogle effectively,
	so it seems to do okay. Apparently Crillic searching is case-sensitive. :/

	enable utf-8 at your site!
*/


// this will change.
$stripped_q = '';


/*
	the corzoogle query..


/*	google rocks! and GET variables rock too - you can see them in server access logs,
	track what folks are searching for, useful info (it will be "?q=sex")	*/
if (isset($_GET['q'])) {

	$q = trim($_GET['q']);
	if (get_magic_quotes_gpc()) {
		$q = stripslashes(trim($_GET['q']));
	} else {
		$q = trim($_GET['q']);
	}

	// this is inserted in the query field, *usually* the last search string
	$result_txt = $q;


/*
	get the query string into shape..	*/

//	query is too short!
	if (strlen($q) < 3) {
		$stripped_q = '';
		$result_txt = 'need three characters!';
	} else {
	//	strip out the stop words; and, the, of, etc
		$stripped_q = strip_stuffing($q);
		$stripped_q = strip_stops($stripped_q);
		if(strlen($stripped_q) == 0) {
			$result_txt = 'more than common words!';
		} else if (strlen($stripped_q) <= 2) {
			$stripped_q = '';
			$result_txt = 'need real words!';
		}

	} // need more definite rules for strings of small "weird" words :2do:


/*	a search for "man+of+war" is now "man+war", "of" was removed. "of"
	will still be taken into account when we match for "phrases", though.	*/


	// create an array of query terms..
	$corzoogle = explode(' ', $stripped_q);	// certainly in the top-ten useful function

	// add query string to the greatest hits..
	if ($recent_hits) latest_hits();


	/*	a few checks..
	*/

//	remove duplicate query terms..
	$corzoogle = array_unique($corzoogle);



//	better do this again
	$qc = count($corzoogle);

	$not_this = '';

/*	-queries .. (boolean NOT)

	don't report files containing query terms preceded by a '-' character
	a search for something like "bandy-legged" would pass unmolested..
*/
		for ($i=0; $i<$qc; $i++) {
			$neg = @$corzoogle[$i];
			if (substr($neg,0,1) == '-') {
				// build an array of NOTs
				$not_this[$i] = substr($corzoogle[$i],1);
				// remove this term from the search array
				unset($corzoogle[$i]);
			} else { $not_this = ''; }
		}


	/*	q may contain boolean NOT's, this will prevent phrase matching of "good" parts.

		when phrase matching, in a search for..

	oh macness -drop

		we'd be looking for "oh macness -drop" instead of just "oh macness".
		this fixes that..

		if someone puts there boolean NOT's in the middle of a query phrase
		they DESERVE to find nothing! but we check for this anyway. *sigh*

	*/
	if (is_array($not_this)) {
		foreach ($not_this as $stripper) {
			$q = str_replace($stripper,'',$q);
			$q = str_replace('-','',$q);
			$q = str_replace('  ',' ',$q);
		}
	}
	$q = trim($q);


/*	filename searching..

	okay, this is neat. if a query term has a word with a dot, it looks like what
	we have here is a filename, so let's also look for a matching file name.
	the engine's running, so let's grab the first filename from the query string
	and get outta here..

	can match "parts" of filenames too.	ie. index. would return
	all "index.whatevers", or any file with the term "index" in its name, as
	well as the usual documents containing that term. .index would achieve the
	same result, but with less regular page hits.


*/
	if ($do_filenames == true) {
		foreach($corzoogle as $q_str) {

			if(strstr($q_str, '.')) {

				if($q_str{0} == '.') {
					$q_str = substr($q_str,1);
				}
				elseif(substr($q_str,-1) == '.') {
					$q_str = substr($q_str,0,-1);
				}
				$sys_filename = $q_str;
				break 1;
			}
		}
	}


/*	someone is searching your files from outside your website!
*/

if (isset($_SERVER['HTTP_REFERER'])) {
	if (!stristr(@$_SERVER['HTTP_REFERER'], $domain) and ($allow_hot_link == false)) {
		$stripped_q = '';
		$result_txt = $hot_link_message;
	}
} elseif ($allow_hot_link == false) {
	$stripped_q = '';
	$result_txt = $hot_link_message;
}


/*	reset the counters
	(i guess you could add extra deepness here if you really needed it)	*/
	if ($deepness) { $path = array('../content/'); } else { $path = array('./'); }
	$hit = 0;
	$fn_count = 0;
}


//	splurge out a nice search form
echoform();


/*
	version info	*/

if (array_key_exists('version',$_GET)) {
	echo '<center><big><code>
corzoogle™ v'.$version.'</code></big></center>';
}

	$cache = acmeCache::fetch($cache_url, $cache_time);
	if(!$cache){

/*
*****************let's spider..	*/

if (($stripped_q != '') and ($qc != 0)) {
	spider();
	/*
		ok, it doesn't actually spider, but it's a fun name for a function nonetheless.
		spidering uses bandwith, for which we are often charged, oh yes.	*/

	// the actual searching happens HERE! --->>> ! <<<---

	/*	dummy function: report_hits()
		i sometimes do this, bbedit has a "functions" menu, cool navigation, heh
		this is the real reason we build bigger, faster computers.	*/
	function report_hits() {}

	//  STOP THE CLOCK!!!
	$search_time = explode(' ',microtime());
	$total_time = ($search_time[1].substr($search_time[0],1)) - $start_time;

	//	report the hits..
	if ($q != '') {

		$plu = '';
		$hc = count($hit_name);
		if ($hc != 1) { $plu = 's'; } // pluralise hit(s) text

		// rank the results, according to $score..
		if (count($score) != false) { // there might be no hits
			array_multisort($score, SORT_DESC, $preview, $hit_name, $pop_title, $file_path); }
			//	php magic!

		// useful corzoogle links. no one sees this unless they corzoogle first!
		if (isset($_GET['q'])) $cache .= do_links();

	/*
	print out query, timer and counter results..
	*/
		$a = '';
		$cache .= '
		<table border=0 cellspacing=0 cellpadding=3 width="81%" align=center>
			<tr>
				<td align="right" valign="top"><h5 style="padding:0px; margin:0px; color:#FFFFFF;">corzoogle found&nbsp;'.
				$hit .'&nbsp;hit'. $plu .' for "';

				// create the "q1+q2+q3" text..
				foreach($corzoogle as $stripped_q) { $a .= $stripped_q .'+'; }

				// remove the last "+"
				$cache .=  substr($a, 0, -1);

				//	report boolean NOT words..
				if ((is_array($not_this)) and (count($not_this) != 0)) {
					$cache .=  '" (not ';
					foreach ($not_this as $notword) {
						$cache .=  ' "'.$notword.'"';
					}
					$cache .=  ') ';
				} else { $cache .=  '"'; }

				$cache .=  ' in '.substr($total_time,0,4).' seconds'.$maxed.'</h5><br>
				<small><font color="#FFFFFF">(searching '.$file_count.' items)
				</small></td>';
				if($hc >= 1) {
					$cache .=  '
				<td width=12></td>'; }
				$cache .=  '
			</tr>
		</table>';


	/*
	stop-words removed from the search? let's report that..
	*/
				$removed = trim($removed);
				if ($removed != '') {
				$cache .=  '
		<table border=0 cellspacing=0 cellpadding=10 width="81%" align=center>
			<tr>
				<td ver="'.$version.'" align="left" id="coremoved" valign="top">
				<font color="#003399">
				<small>
				the following common words were removed from the search: <b>'.$removed.'</b>
				</small>
				</td>';
				if($hc >= 1) {
					$cache .=  '<td width=12></td>'; }
				$cache .=  '
			</tr>
		</table>'; }



	/*
	splurge out results..
	*/
		for ($i=0; $i<$hit; $i++) {
		//	if($i>=$max_hits) break;
			$link = dozooglelink ($file_path[$i]);
			$cache .=  '
			<table border=0 cellspacing=0 cellpadding=10 width="81%" align=center>
				<tr>
					<td align="left" id="zooglehit" colspan=2>
					<a href="'. $link.'" title="'.$pop_title[$i].'"><h5 style="padding:0px; margin:0px;">'.$hit_name[$i].'</h5></a>
					'.$preview[$i].'<br>';
					if ($show_scores) $cache .= '
					<b><small>score: </small></b>'.$score[$i];
					$cache .=  '
					</td>
				</tr>
				<tr>
					<td height=10></td>
				</tr>
			</table>
			';
		}



	/*
	additionally, we found the following matching filenames..
	*/
		if ($sys_filename == true) {
			if(count($filename_hit) > 0) {
			$cache .=  '
			<table border=0 cellspacing=0 cellpadding=0 width="81%" align=center>
				<tr>
					<td width=12></td>
					<td align=left id="corfile">
					<b><small style="color:#FFFFFF">corzoogled '.$fn_count.' file names matching "'.
					$sys_filename .'".. </b></small><br><br>
					</td>
				</tr>
				<tr>
				<td width=12></td>
					<td>';
					for ($i=0;$i<$fn_count;$i++) {	// test speed of list/each combo
						if ($filename_hit[$i] != '') {
							$link = dozooglelink ($filename_hit[$i]);
							$cache .=  '<a href="'.$link.'" title="'.
							basename($filename_hit_name[$i]).'"><h5 style="padding:0px; margin:0px; color:#FFFFFF;">'
							.$filename_hit_name[$i].'&nbsp;</h5><br></a>';
						}
					}
					$cache .=  '
					</td>
				</tr>
				<tr>
					<td height=33></td>
				</tr>
			</table>';
			 }
		}

	/*
	*********show most recent searches, just for fun..
	*/
		if ($recent_hits == true) {
		$cache .= '<!-- really, it\'s time to bring back the <blink> tag -->
		<table border=0 cellspacing=0 cellpadding=3 width="81%" align=center>

		<tr>
			<td height=20></td>
		</tr>
		<tr>
			<td height=30 valign=bottom align=right><b>most recent searches<br><small>';
			foreach ($past_hits as $haha) {
				$cache .= '&nbsp;<a href="'. $_SERVER['SCRIPT_NAME']
				.'?q='.str_replace(' '. '+'. $haha).'" title="'.$haha .'">'.$haha.'</a>';
				}
			$cache .= '</small></b>
			</td>
		</tr>
		</table>';
		}
	}
}

	$cache .= "\r\n<!-- CACHE:Saved ".date('r')." -->\r\n";
	
		acmeCache::save($cache_url, str_ireplace(substr($total_time,0,4).' seconds',"cached search results",$cache));
		
	} else {
	
		$cache .= "<!-- CACHE:Read  ".date('r')." -->\r\n";
	}
	
	echo($cache);


// whatever, we need a gap at the bottom. whitespace is important..
echo '
<!-- corzoogled! --><table>
	<tr><td height=90></td></tr>
</table>';

//	:todo:
// perhaps put the menu (css) bottom left for first page visits (no searches yet)
// i spotted more than one person visit corzoogle, then come back again to the
// download page from a GOOGLE search for "corzoogle download". how stupid is that! hahah


// feed the browser..
ob_end_flush();

// send mail to caring webmasters..
if (($notify == true) and ($stripped_q != '')) { notify_webmaster(); }


// my footer..
if (($embedded != true) and (stristr($_SERVER['HTTP_HOST'],'corz'))) {
	@include($_SERVER['DOCUMENT_ROOT'].'/inc/footer.php'); }



/*
	that's all folks. now the funky functions.
	I like them underneath
*/


/*
	function:spider()
	could make the basis of a handy class, a tool for general burrowing
	I've nicked it for a links machine already. would need bulletproofing, though.
*/
function spider() {
global $domain, $extentions, $file_count, $sys_filename, $filename_hit, $filename_hit_name, $fn_count, $hit, $ignore, $level, $maxed, $max_hits, $path, $private;

	// build a string of the paths inside this root..
	$search_path='';
	for ($i=0;$i<=$level;$i++) {
		$search_path .= $path[$i];

		// remove those important IGNORE directories from the search path..
		$search_path = str_replace($ignore, '', $search_path);
	}

	// go in and read the directory's contents..
	$dirhandle = opendir($search_path);
	while ($file = readdir($dirhandle)) {

	// skip 'dir' entries and invisibles (esp' mac ._resource.frk files)
	if ($file{0} != '.') { //	a quick way to grab the 1st character

	$file_count++; //	totale grandé

		// if it's a "regular file"..
		if (is_file($search_path.$file)) {

			// is this a $sys_filename I see before me?
			if($sys_filename == true) {

				// if you have a version of php that throws up many empty delimiter errors..
				// this will be a CaSe SensitIvE match..
				//if($safe_level > 0) {
				//	if(strpos($file, $sys_filename) !== false) {
				//} else { V }

				// .gif would find all .gif .GIF and .Gif files	(the default, quickest method)
				if(stristr($file, $sys_filename)) {

				// for exact matching filenames..
				//if($file == $sys_filename) {
				//


					//	apache array ("a patchy array", heh. it isn't anymore :)
					$filename_hit[$fn_count] = $search_path.$file;
					$filename_hit_name[$fn_count++] = 'http://'.$domain.substr($search_path.$file,1);
				}
			}

			//	get the extension..

			//	fastest..
				$fext = substr($file,strrpos($file,'.'));

			/* if we are permitted to search this filetype, then do it..
						the answer could be '0' here, is why the triple ===	*/
			if((strpos($extentions, $fext)) and (strpos($private, $file) === false)) {
				look_in_file($search_path.$file);
			}

			if ($hit >= $max_hits) {
				$maxed = ' (search limit reached)';
				return;
			}
		}

		// or is it a directory? if so, add it to the path and loop into it.
		elseif (is_dir($search_path.$file)) {
			$path[++$level] = ($file.'/');
			spider();
			$level--;
			}
		}
	}
	closedir($dirhandle);
}/*
	end function:spider()
*/



/*
	function:look_in_file($file)
*/
function look_in_file ($file) {
global $arc_name, $blog_ext, $blogz_path, $cat_length, $contents_as_title, $context_ratio, $corzoogle, $deepness, $default_boolean, $domain, $dont_search, $pop_title, $enforce_between, $file_path, $hit_name, $sys_filename, $filename_hit, $filename_bonus, $highlight_exact_hits, $hit, $level, $mangle, $match_phrases, $not_this, $path, $phrase_max_score, $preview, $preview_has_context, $q, $qc, $q_max_score, $q_word_max, $weighted_scoring, $safe_level, $score, $score_all_titles, $search_between, $sbtici, $search_in_tags, $snippet_length, $title_scoring, $title_max_score, $use_descriptions, $use_snippets, $weight; // yeah, it's gettin outta hand

	// basic notice avoidance..
	$term_tally = 0;
	$title = '';
	$in_title= false;

	// that's another fine $search_path you've gotten me into..
	$search_path='';
	for ($i=0; $i <= $level; $i++) {
		$search_path .= $path[$i];
	}



	/*
		where corzoogle opens and reads the file..

		$file_data = the raw text of the file, remains constant
		$search_data = the (modified) text we will be searching through
	*/


	if ($safe_level > 0) {
		$search_data = $file_data = implode('', file($file));	// 50% slower!
	} else {
		//	the 'b' is for windoze
		$file_handle = fopen($file, 'rb');
		if (filesize($file) > 0) {
			$search_data = $file_data = fread($file_handle, filesize($file));	// we'll use both
		} else { $search_data = $file_data = ''; }
		fclose($file_handle);
	}

	$file_name = basename($file);


	/*	boolean NOT checking..

		multiple -not terms are checked..
		if the file contains one of your -terms, it's damned!
	*/
	$damned = false;
	if (is_array($not_this)) {
		foreach($not_this as $notz) {
			if(stristr($search_data, $notz)) { $damned = true; break 1; } else { $damned = false; }
		}
	}


	/*	"nosearch" tag?..

		is the word "nosearch" (or your custom $dont_search word) inside this file?
		if not, and it's not damned, we will investigate further..		*/
	if (($damned == false) and (strpos($file_data, $dont_search) === false )) {



		/*
			we have a candidate!	*/



		/*	search_between..

			if there's a $search_between tag in the document, use it to search between..
			(tags configurable in prefs) default is search between <body></body> tags.	*/


		/*	neater, but slower..

			if Search Between Tags Is Case Insensitive:	*/

		if ($sbtici == true) {
			if (count($search_between) != 0) {
				if (!strpos($file_data, $search_between[0]) === false ) {
					if ($between_str = stristr($file_data,$search_between[0])) {

						$end_point = strpos($between_str, $search_between[1]);
						if ($end_point == false) { $end_point = strlen($between_str) + 1; }

						$search_data = substr($between_str, // <- the string
						strpos($between_str,'>') + 1, // <- start (after starting tag)
						$end_point - strpos($between_str,'>') - 1); // <- length

					} elseif ($enforce_between == true) return;
				}
			}
		} else {

			// uglier, faster..
			if (count($search_between) != 0) {
				if (!strpos($file_data, $search_between[0]) === false ) {

					$s_start = strpos ($file_data, $search_between[0]) + strlen($search_between[0]) + 1;
					$end_point = strpos($file_data, $search_between[1]);
					if ($end_point === false)  $end_point = strlen($file_data) + 1;
					// reconstruct the tag, it can be stripped away later with strip_tags()
					$search_data = $search_between[0].'>'.substr($file_data, $s_start, $end_point - $s_start);
				} elseif ($enforce_between == true) return;
			}
		}


	/*	if the file has a real <title>, we'll use it, and score for it..
		if not, we'll use the filename in its place. getting the <title>
		this early on causes a :speed: hit, usually worth it.	*/

		// if the filename becomes the title, we'll chop off the extension..
		$name_is_title = false;
		if ($score_all_titles == true) {
			if (!$title = get_title($file_data))  {
				$name_is_title = true;
				if ($contents_as_title == true) {
					$title = substr(strip_tags($search_data),0,$cat_length).'...';
				} else {
					$title = substr($file_name,0,strrpos($file_name,'.'));
				}
			}
		}


	/*	regardless of the number of query terms, we want a $snippet_length
		character preview (default is 300)	*/
		$num_o_q = count($corzoogle);	// number of query terms
		$preview_length = ($snippet_length / $num_o_q); // simple arithmetic
		$snippet = '';
		$found = 0;

		/*
		score progressively less for 2nd, 3rd and 4th query terms..		*/
		if ($weighted_scoring) { $i = $weight; } else { $i = 3; }


		/*
				where corzoogle examines the file contents...	
																	*/

		/*
		check query terms one-by-one, (walk through the $corzoogle array)	*/

		foreach($corzoogle as $q_str) {

			// it was a one-line feature. tag-less version of the document
			if ($search_in_tags != true) { $search_data = strip_tags($search_data); }

			//	kill two birds with one stone..

			//	the most important line of the program
			if ($match_str = stristr($search_data, $q_str)) {

				$match_pos = strpos($search_data, $match_str);
				$found++;

				// this makes the preview more understandable, understandably.
				if ($preview_has_context == true) {
					$snip_from = $match_pos - ($preview_length/$context_ratio);
					if ($snip_from < 0) $snip_from = 0;
					$snippet .= substr($search_data, $snip_from, $preview_length) .'.. ';
				} else {
					$snippet .= substr($match_str, 0, $preview_length) .'.. ';
				} //only on word boundries..
				$snippet = substr(strip_tags($snippet), strpos($snippet, ' ') + 1);

				// make that three birds

				$term_scored = 0;

				//	$term_scored += ($i * substr_count($search_data, $q_str));

				/* a wee bonus for case-sensitive exact match..	*/
				$exact_bonus = substr_count($search_data, $q_str);
				if ($exact_bonus != 0) { $term_scored += 1; }

				$term_scored += ($i * $exact_bonus);

				if (ucfirst($q_str) != $q_str) {
					$term_scored += ($i * substr_count($search_data, ucfirst($q_str))); // handy function
				}
				if (strtolower($q_str) != $q_str) {
					$term_scored += ($i * substr_count($search_data, strtolower($q_str)));
				}
				if (strtoupper($q_str) != $q_str) {
					$term_scored += ($i * substr_count($search_data, strtoupper($q_str)));
				}

				if ($term_scored == 0) {
					$term_scored += ($i *
					substr_count($search_data, substr($match_str,0,strpos($match_str,' '))));
				}

				//	max $q_word_max points per query term (set in prefs)..
				if ($term_scored > $q_word_max) $term_scored = $q_word_max;

				// if weighted, after 4th term, all matches score one point
				// these terms are the bit-players in our search adventure
				if ($weighted_scoring) {
					$i--;
					if($i == 0) $i++;
				}
				$term_tally += $term_scored; // add this query term's score to the tally
			}

			// you can only score so many points by way of query terms (set in prefs)..
			if ($term_tally > $q_max_score) { $q_scored = $q_max_score;} else { $q_scored = $term_tally; }

			/* okay, four birds.
			if the file has a real <title>, we'll use it, and score for it..*/
			if (($score_all_titles == true) and ($title_scoring == true) and (stristr($title, $q_str))) {
				$in_title = true;
			}
		}// finished walking $corzoogle array now


		/*
		if *all* the query terms didn't appear inside this file, go back to spider()ing
		it is _crucial_ that we return at this point for NON-matching files. :speed:	*/

		if (($found != $num_o_q) and ($in_title == false)) return;

		// or else continue, it's a $hit!..

		/*
		filename bonus.. if any of the query terms are in the filename
		*/
		if (($sys_filename) and ($name_is_title == false)) {
		foreach($corzoogle as $q_str) {
				if(stristr($file, $q_str)) {
					$q_scored += $filename_bonus;
				}
			}
		}


		/*
		phrase matching..	*/

		$phrase_scored = 0;
		if ($match_phrases == true) {
			//*	if the original query is a phrase (has a space inside it)
			if (strrpos($q, ' ')) {

			/*
			even if "and", "am", "I" or whatever (stop words) were removed from the query array,
			we still use the *entire* query string when looking for matching *phrases*.
			you only get this far if we already have a document matching all terms.
			*/

				//	query exists (with the SaMe cAsE) *in entirety* inside this file, +30% (jackpot!)
				if (strpos($search_data, $q)) {
					$phrase_scored += ($phrase_max_score * (3/10));

					// the longer the query, the higher the bonus
					$phrase_scored += (substr_count($q,chr(32))*($phrase_max_score * (1/10)));

				//	or +20% for a phrase match with a different CaSe..
				} elseif(stristr($search_data, $q)) {

					//	"man mac" would match "The BIG Man Machine ..."
					$phrase_scored += ($phrase_max_score * (2/10));
					$phrase_scored += (substr_count($q,chr(32))	*5);
				}
			}


		// up to the maximum set in the preferences..
		if ($phrase_scored > $phrase_max_score) $phrase_scored = $phrase_max_score;

		// consider "fuzzy" searching here.	:2do:

		// now we set the total score for terms..
		$score[$hit] = $q_scored + $phrase_scored;


			}
		// we'll use this for our links..
		$file_path[$hit] = $file;


		/*
		create a preview snippet..	*/

		if ($use_snippets == true) {
			$text_preview = $snippet;
			//	sometimes we still have a tiny string. like when the query matches right at the end.
			//	so we'll just pad that out with some body text instead..
			if (strlen($text_preview) < 33) {
				$text_preview = strip_tags($search_data);
				$text_preview .= substr($text_preview, 0, $snippet_length);
			}
		}


		/*
		description	*/

		$description_tag = '';
		//	are we using these in results?
		if ($use_descriptions == true) {
			// if so, does this document have a <description> tag?
			$description_tag = get_description($file_data);
			// if all that worked out we'll put this description below our preview title
			if(strlen($description_tag) > 0) {
				$description_tag = '<div id="description">'.$description_tag.'.. </div>';
				$text_preview = $description_tag.$text_preview;
			}
		}



		/*
		just for looks..	*/

		//	this will prevent us getting <- W I D E -> previews..
		$wrappers = array('>','<','_','.','(','-');
		$splitter = array('> ',' <',' _','. ',' (','- ');
		$text_preview = str_replace($wrappers, $splitter, $text_preview);

		/*	colour the found query terms by their capitalisation..
			this technique works fine, looks nice, but it's a bit boring	*/
		foreach ($corzoogle as $rep) {
			$text_preview = str_replace(strtolower($rep),
			'<font color="#FEFEFE">'. strtolower($rep) .'</font>', $text_preview);
			$text_preview = str_replace(ucfirst($rep),
			'<font color="#339900">'. ucfirst($rep) .'</font>', $text_preview);
			$text_preview = str_replace(strtoupper($rep),
			'<font color="#CC3333">'. strtoupper($rep) .'</font>', $text_preview);

			//	you can add ugly highlights, too..
			if ($highlight_exact_hits == true) {
				$text_preview = str_replace($rep,
				'<span style="background: #FFFF33;"><font color="#333333">'. $rep
				.'</font></span>', $text_preview);
			}
		}


		/*
			the preview is ready	*/

		$preview[$hit] = stripslashes($text_preview);



		/*
		corzblog	*/

		if (substr($file, (0 - strlen($blog_ext))) == $blog_ext) {
			$blogz_path = '';
			$pop_title[$hit] = str_replace($blog_ext, '', basename($file));
			$hit_name[$hit] = 'corzblog archive: '. $pop_title[$hit];

			// work out the path parts we need..
			$this_blog_path = dirname($file);
			$blog_path = explode('/', $this_blog_path); // split it up
			foreach ($blog_path as $element) {
				if(stristr($element,$arc_name)) break;
				$blogz_path .= $element.'/';	// put it back together
			}
			$file_path[$hit] = $blogz_path .'index.php?archive='. $pop_title[$hit];
			$is_blog = true;
		} else { $is_blog = false; }
	 	/*
		end corzblog specific code	*/


		if($is_blog == false) {



			/*
			extension mangling	*/
			

			if (count($mangle) != 0) {
				reset($mangle);
				while (list($key, $value) = each($mangle)) {
					if(substr($file_name, (0 - strlen($key))) == $key) {
						//$file_name = str_replace($key,$value,$file_name);
						$file_path[$hit] = str_replace($key,$value,$file_path[$hit]);
   					}
				}
			}


			$pop_title[$hit] = $file_name;


			/*
			<title> scoring..	*/

			if (!$score_all_titles == true) {
				if (!$title = get_title($file_data))  $title = substr($file_name,0,strrpos($file_name,'.'));
			}
			if ($title_scoring) {
			/*
				an important small (stop) word may have been removed. but such a word
				can be important in the "phrase", so we use the whole query string.
			*/


				//	phrase in the title..
				$t_scored = 0;

				//	check that q is a phrase. (another way to do this)
				if ((strrpos($q, ' ') > 0)) {

				/*	the whole query string matches a part of the title (case insensitive)
					a search for "dog spot" would return the "my dog spot" page, along
					with the usual dog spot documents

					this is stylish, huh. and what a punchline! obfuscated php..
					if(stristr(substr($title,strpos($title,$q),strlen($q)),$title{0}) == $q) $t_scored+=20;

					*ahem*, where was I..
					ah yes, this three-stage approach works well..	*/

					//	the *direct hit* .. full query matches the title exacitaly!
					if ($title == $q) {
						$t_scored = $title_max_score;

					// case-insensitive exact match
					} elseif (strlen(stristr($title, $q)) == strlen($title))  {
						$t_scored += ($title_max_score*(3/5));

					//	now a case-insensitive full title match..
					} elseif (stristr($title, $q)) {
						$t_scored += ($title_max_score*(2/5));
					}
				}

				//	no phrases, okay, check for individual words in the title..
				$tmp_score = 0;
				if ($t_scored == 0) {

					// +20% for each query term that's in there *somewhere*..
					foreach ($corzoogle as $q_str) {
						if (stristr($title, $q_str)) $tmp_score += ($title_max_score/5);
					}

					//	up to a maximum of 80% of $title_max_score, a direct hit will always score higher.
					if ($tmp_score > ($title_max_score*(4/5))) $tmp_score = ($title_max_score*(4/5));
					$t_scored += $tmp_score;
				}

			if ($t_scored > $title_max_score) $t_scored = $title_max_score;



			//	add the title score to the running total..
			$score[$hit] += $t_scored;

			} // finished title scoring


			// if there is no title, or it contains a variable, the filename will do fine
			if (strpos($title, '$') > -1 ) {
				$hit_name[$hit] = $file_name; }
			else {
				$hit_name[$hit] = $title; }

			} // not a blog

		// increase our hit counter for the next (possible) hit..
		$hit++;

	} else return; /*	it's a $dont_search or -query found, back to spider()ing	*/
}/*
	end function:look_in_file($file)
*/



/*
function:notify_webmaster()	*/
function notify_webmaster() {
global $q, $to_addy, $email_subject, $email_body, $from_addy, $hit, $version, $xmail_headers;

	// as you can see, php mail is easy..
	if (isset($_SERVER['HTTP_REFERER']) and ($_SERVER['HTTP_REFERER'] != $_SERVER['HTTP_HOST'] )) {
		$refer = '\nrefered by: '.$_SERVER['HTTP_REFERER']."\n";
	} else $refer = '';
	$xmail_headers .= "From: $from_addy\r\nX-Mailer: corzoogle notify\r\n";
	$email_body = $email_body."\nThe search query was..  \"$q\"  [$hit hits]\n
--\ncorzoogle mailer v$version (c) corz.org\r\n";
	// but that still doesn't mean it will work!
	mail($to_addy, $email_subject, $email_body, $xmail_headers);

}/*
end function:notify_webmaster()	*/



/*
	function:latest_hits()
	interestingly, this affects folks' search queries..
*/
function latest_hits () {
	global $hits_length, $past_hits, $q, $stripped_q;



	// add our query string to the top of the array,
	array_push($past_hits, $stripped_q);

	// get the "serialized" array from the ".corzoogles" file
	if (file_exists('.corzoogles')) {
		$grab_hits = implode('', file('.corzoogles'));
		$tonti = unserialize($grab_hits);

		// join the unserialized array to our $past_hits array
		if (is_array($tonti)) { // might be a new file
			foreach ($tonti as $vamo) {
				/* 	it's best to do it one element at a time here, the array is very small*/
				array_push($past_hits, $vamo);
				if (strlen(implode('', $past_hits)) > $hits_length) {
					break 1;
				}
			}
		}
	}

	//serialise and save the whole array back to a file for the next search. and so on ...
	$sa = serialize($past_hits);

	// attempt to create file if it doesn't exist
	$fp = @fopen('.corzoogles', 'w');
	if (is_writable('.corzoogles')) {
		flock($fp, LOCK_EX);// not really necessary here, but still..
		fputs($fp, $sa);
		flock($fp, LOCK_UN);
		fclose($fp);
		clearstatcache();
	} else {
		// if we can't create the file, guide the user to a solution..
		echo '
		<table border=0 cellspacing=0 cellpadding=10 width="81%" align=center>
		<tr>
			<td height=300>
			<h3>your .corzoogles file is not writable! &nbsp;&nbsp; recent seaches cannot be stored!</h3><br>
			<kbd>please manually create the ".corzoogles" file, and make it world-writeable**,<br>
			or allow (temporary) write access to the directory that I live in, so I can make the file myself.
			<br><br>
			**<br>
			<font size="-1" color="#325976">
			you can give the folder write-access in a shell/terminal..
			<b>sudo chmod 777 /path/to/folder</b><br>
			or from your desktop, using the properties or "get info" for that folder<br><br>
			or with any decent text editor, save a blank ".corzoogles" file next to "corzoogle.php"<br><br>
			you could do the whole lot in a shell, too..<br>
			<blockquote>
			<b>sudo echo > /path/to/folder/.corzoogles<br>
			sudo chmod 777 /path/to/folder/.corzoogles</b>
			</blockquote>
			alternatively, just switch off recent searches in the preferences!</kbd>
			</font>
			</td>
		</tr>
		</table>';
	}
}/*
	end function:latest_hits()
*/



/*	
	function:echoform()

	with a slight alteration, you can easily use this on other pages, too
	like your 404 page, for instance. with maybe something like..

	$insert = substr($_SERVER['REQUEST_URI'], (strrpos($_SERVER['REQUEST_URI'], '/')+1));
	form input value="'. $insert .'"; 	(pseudo code, you get the idea)

	hit a 404 at corz.org for a demo. I use a static png logog onsite, btw.
	you can download 404.php from http://corz.org/engine

	note: if you use corzoogle on the web, you must leave the copyright notice
	(below) intact, unless you purchase a deluxe license, of course.

*/
function echoform() {
global $embedded, $logo, $result_txt, $redirect_to, $redirect;

	$do_image = false;
	if (($embedded != true ) and ($logo == 'embedded')) { $do_image = true; }
	if ($logo =='embedded') { $logo = $_SERVER['PHP_SELF'] .'?zoogleimg=go'; }

	if ($redirect) { $target = $redirect_to; } else { $target = $_SERVER['PHP_SELF']; }

	echo'
<form method="get" action="',$target,'" name="corzoogle">
<table id="corz" border=0  width="100%" cellspacing=0 cellpadding=0>
	<tr>
		<td height=42>
		<!-- corzoogle™ powered search © corz.org 2004 >> -->
		<!-- you cannot legally remove this copyright notice -->
		</td>
	</tr>
	<tr>
		<td align=center valign=bottom>
		<a href="',$_SERVER['PHP_SELF'],'"
		title="fast realtime personal search engine from corz.org">
		<img src="',$logo,'" border=0 
		alt="logo for corzoogle; 
the fast real-time personal search engine from corz.org"></a>
		<br><br>
		<input type="text" name="q" size="21" maxlength="256" value="',$result_txt,'"
		title="you can narrow your search by using multiple terms,
also use -words to NOT search for particular words
click the \'tips\' link for more info">
		&nbsp;<input type="submit" value="do it!" title="corzoogle locates!"><br>
		</td>
	</tr>
	<tr>
		<td height=54></td>
	</tr>
</table>
</form>
';

}/*
	end function echoform()
*/



/*
	function:do_header()
*/
function do_header() {
global $use_utf8; echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd"><html><head>';
if ($use_utf8 = true) echo '<meta http-equiv=content-type content="text/html; charset=utf-8">';
echo '
<title>corzoogle - the fast, real-time search engine</title><meta name="description" content="corzoogle - fast realtime personal search engine for home or website, from corz.org. fast. portable. cool."><meta name="keywords" content="corz,search engine script for home or website,fast,personal,text,search,engine,php,archive,scanner,live,real time,real,time,PHP,website,personal archive search engine,searching,easy,simple,install,simple install,where can I get free search engine,looking for fast free search engine,here,HERE!data,mining,data-mining"><meta name="generator" content="corzoogle"><meta name="author" content="corz.org"><link href="/inc/osx.css" rel="stylesheet" type="text/css">
<style type="text/css"><!--
body {
	font-family: Tahoma, Lucida Grande, Helvetica, Verdana, sans-serif;
	font-size: small;
	color: #003;
	background: #5482A7  url(/img/window_plain_m.png) repeat-x;
}
td.foot {
	font-family: Tahoma, Lucida Grande, "Trebuchet MS", Trebuchet, Verdana, Helvetica, sans-serif;
	font-size: 11pt;
	color: #000;
	padding: 3px;
}
a:link		{ color: #FFFFFF; text-decoration: none; }
a:active	{ color: #FFFFFF; text-decoration: none; }
a:visited	{ color: #FFFFFF; text-decoration: none; }
a:hover		{ color: #FFFFFF; text-decoration: underline; }

#description {
	color: #336;
	font-size: x-small;
	text-decoration: none;
}
//--></style>
</head>
<body onLoad="javascript:document.corzoogle.q.select(0)" leftmargin=0 rightmargin=0 topmargin=0 bottommargin=0 marginwidth=0 marginheight=0>';

/*	my include. you'll probably want your own.	*/
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/inc/errheader.php')) 	{ 
		include($_SERVER['DOCUMENT_ROOT'].'/inc/errheader.php');
	}
}/*
	end function:do_header()
*/


/*
	function:do_links()

	if you pay, you can remove these links. but even then, please do leave a
	link to the "tips" somewhere, which I will update. in fact, what I'll do
	is create http://corz.org/corzoogle/tips.txt and give it minimal html
	formatting, simple table rows, so you can load it in an iframe or whatever,
	users do deserve the latest and best help available.

	if you think you can improve on the tips, or any other thing, by all means do.
*/
function do_links() {
global $hc;
$result = '
<table border=0 cellspacing=0 cellpadding=3 width="81%" align=center>
	<tr>
		<td align=right>
		<small><b><a href="http://corz.org/corzoogle/index.php"
		title="fit\'s it a aboot?">about</a>:<a href="http://corz.org/corzoogle/download.php"
		title="download corzoogle for your own home or website"
		>download</a>:<a href="http://corz.org/corzoogle/tips.php"
		title="handy tips, ways to get the most from your corzoogling">tips</a>
		</b></small>
		</td>';
		if ($hc >= 1) {
			$result .= '<td width=12></td>'; }
		$result .= '
	</tr>
</table>
';
return $result;
}/*
	end function:do_links()
*/

/*
	function:get_description()
	we can (optionally) display these under the titles of results
*/
function get_description ($document) {

	$description_tag = '';
	//	the easy but mega-slow method..
	//	$meta_tags = get_meta_tags($file);
	//	etc. go no further! it's too damned slow!

	//	the (much) quick(er) way.. not clever, but trés rapido!
	//	(must avoid regex at all costs - no stripos for php4)

	if (strpos($document, 'meta name="description"')) {
		$start_at = strpos($document, 'meta name="description"')+33;
		$content = substr($document, $start_at, 333); // 333 characters maximum description
		$end_at = strpos($content, '"');
		$description_tag = substr($content, 0, $end_at);
	}
	if (strpos($document, 'META NAME="description"')) {
		$start_at = strpos($document, 'META NAME="description"')+33;
		$content = substr($document, $start_at, 333);
		$end_at = strpos($content, '"');
		$description_tag = substr($content, 0, $end_at);
	}
	if (strpos($document, 'META Name="description"')) {
		$start_at = strpos($document, 'META Name="description"')+33;
		$content = substr($document, $start_at, 333);
		$end_at = strpos($content, '"');
		$description_tag = substr($content, 0, $end_at);
	}
	if (strpos($document, 'meta name=description')) {
		$start_at = strpos($document, 'meta name=description')+30;
		$content = substr($document, $start_at, 333);
		$end_at = strpos($content, '>');
		$description_tag = substr($content, 0, $end_at);
	}
	if (strpos($document, 'META NAME=description')) {
		$start_at = strpos($document, 'META NAME=description')+30;
		$content = substr($document, $start_at, 333);
		$end_at = strpos($content, '>');
		$description_tag = substr($content, 0, $end_at);
	}
	if (strpos($document, 'META Name=description')) {
		$start_at = strpos($document, 'META Name=description')+30;
		$content = substr($document, $start_at, 333);
		$end_at = strpos($content, '>');
		$description_tag = substr($content, 0, $end_at);
	}

	return $description_tag;
}/*
	end function:get_description()
*/



/*
	function:strip_stops()
	accepts $string, returns $string minus any "stop-words"
*/
function strip_stops($string) {
global $removed, $stop_words;

	$words = explode (' ', $string);

	// stop-words themselves are up in the prefs now.

	$qs = count($stop_words);
	reset($words);
	while (list($key, $val) = each($words)) {
		for($i=0;$i<$qs;$i++) {
			if (($words[$key] == $stop_words[$i]) or ($words[$key] == ucfirst($stop_words[$i]))) {
				unset($words[$key]);
				$removed .=' '.$stop_words[$i];
				break 1;
			}
			elseif (strlen($words[$key]) < 2) {
				//$removed .=' '.$words[$key];
				unset($words[$key]);
				break 1;
			}
		}
	}
	return implode(' ', $words);
}/*
	end function:strip_stops()
*/


/*
function strip_stuffing() 	*/
function strip_stuffing($stripped_q) {
	//	there are lots of silly people about, so we remove a few undesirables, there will be more.
	$nonos = array('<','>','..',' .'.'. ',',',';','[',']','\\',' \\',
	'\\ ','/',' /','/ ','*','~','#','•','°',
	"\n","\r","\t","\r\n",'&','?','$','%','+','=','»','«');	// we leave :()" for now. might need those
	$stripped_q = str_replace($nonos, '', $stripped_q);	// remove undesirables

	// in case folks have extra spaces in between the words..
	$spacers = array('     ','    ','   ','  ');	//	goan remove one. I dare ye!
	$stripped_q = str_replace($spacers, ' ', $stripped_q);	//	str_replace has back-to-front syntax.
	$stripped_q = str_replace('+', ' ', $stripped_q);	//	yes, I know *why*. it's still annoying.
	$stripped_q = trim($stripped_q);	//	might add "te rms" in the future, hmm..
	return $stripped_q;
}/*
end function strip_stuffing() 	*/



/*
function:get_title()
something like if(eregi ("<title>(.*)</title>", etc.. is less reliable
*/
function get_title($string) {
	$t_end = '';
	if ($grab_this = stristr($string, '<title>')) {
		$grab_this = substr($grab_this, 7, 1024);
		$t_end = strpos ($grab_this, '<');
	}
	return substr($grab_this, 0, $t_end);
}/*
end function get_title()
*/



/*
	function:zoogleimg() - embeded corzoogle image

	Thanks to Rolf Holtsmark and Terje Monsen..	http://php.holtsmark.no
	I knew that *out there* somewhere, would be tool for doing this.
*/

function zoogleimg()  {
    header("content-type: image/png");
    header("content-length: 16030");
//	header("Content-disposition: attachment; filename=corzoogle.png");
    echo base64_decode(
	'iVBORw0KGgoAAAANSUhEUgAAAPwAAABGCAIAAABnmG4jAAAgAElEQVR42uy9abidZXkvfj/jO6557Xnv'.
	'ZO/MBAiEUQTFARRtncCqoNXWWq2n6lHr0HraOpza2lY9dlKkWquIAwoO1IFBEJAAIYRMJNkZd/Y8rHmt'.
	'd3ym/4cVQsJksP1zrqsn95UP+bDWu97neX73/LufjYwxcEpOyf8zorWgp3bhlPzfEpNoZJAygjCusUYI'.
	'IYSeg9/Fp7b+lDz30qy1Zqfm6o3KzVt+ft7Hf/vunZswxkKK5ybuOGXpT8lzJYmYb7Vn20EzToLOYl/e'.
	'm5ibPlQ50j8wWAub0IEUUoIJIeQU6E/Jfwdpt9oPzCzkLeI5rJixZbF/sVmzgfnE1kp7yK4szuGC5TjO'.
	'qfDmlPw3ke3VdsnDIwWWZ0ZrHSaJFjpKo0rYWAwrHHCj3dRGPzfhzSnQn5LnQp7fVwhjND4fN6XWWhol'.
	'EyMSkbSDoBq1cnY2TENk0HPzMqdAf0qeE7H4JYM92Tg8eLiWalAapEpSoYK03YobPZms1gYTfKp6c0r+'.
	'+4gBiBhbPdxDsU6M1kYppZUWUukgCbYdns05noHnyNKfSmRPyXMSUWDMGSPZHLfqWiillRA61jJOQwAl'.
	'7DwgifEp0J+S/0aCECIIgeVQxqVOtFIKUqF1LBKZapcTYsAAnApvTsl/Q/ADAqVUqpNEaqGkECkICZho'.
	'o587t3PqHE7JcxvdG6W1VEZroVMRqBRhig08V/H8KdB3RZlOE0AZADhFv3sOTL1UUog0EiI2QiuBDAV4'.
	'TlH/rGL6GEBLbSggwAjA/s+8qAboaKE1MgQTABth/l9hRRbbca9nATlBmdPIpBh8jp7ifRX8fOfeB+N7'.
	'X1y4/IVrxzQY8v/v7muIEo2MMUAIAGFA2H+5EndMarRRCFOsGVAL+H/Vo+NERqkQUhOCKEI+58Qiz/KI'.
	'kNRpLFIh0lREqQKmlcEInrW5MRpEYlINiCBkA4Wjy/z1+vPMoNdKhQfqnZmlVq0lgoZGRmiFLQ8bxGwH'.
	'Slm6aig3lPEB3JPU1X2pWJirx4tLTAGJQtBKx4nmVGAOnkOzTr6/dzRDi4w92yS7XmtsnVvkqnHQ7b1o'.
	'bBkQBEJum63PN+pINkUUtAVki6OXrBnLOUZrgzEGgImp1hzec17OmRdLR3Y7I4MO2BlgGAw8xTEQBBhA'.
	'ASCABJIYBICfAaBgjHn6JMwEnfb+Snu60ogNSbUArdNUYoyNMY7lFC08XHRHchnP935THTATSXu22grm'.
	'A6wMjYyRqU6EJlhQZGzKPJot55blnQHXA/QsdUDpg0vN+XrLiIAgQ5BCWBqNtQFhiDaIWrmSb63Ie9z3'.
	'fu3DCMKxEpEIkjQRqdKgwDyrcCM50GrNzbTDUEICKEpFJxEAghhc8HJlvHrt8HLwuuoFT3MiTwcsM1tb'.
	'3DnbWpyLwrlIt9OwHlGfYooZAgnY9XjL6MUENrPFnhHv0gv7xnJ5oy14GnaoBNjUCGqbt2cPTmSpKlJi'.
	'YwSUKyVVKlUUmjDU3FLckl7m0eERMVge7cuuyOeB0JNRpl1HZuphMOYmTKsjYT1oFgIhH1po9KClQdMQ'.
	'ohUlTdyYrey+4evjF7/2it9f5qYGMaTxtupczg4pLmdgaYem2yZdRKaJAQXAMRhA2IABjQBLMFIppaWR'.
	'KEJCqzCQ9Xq6ZEPfH254hZ9FGpOuIh0vs9X6rbuPLCxWbQo+pwwBJkQb0zV0QinOaMtxp+v2NreVsdmG'.
	'gjsy2APUOnkUbA2qE9tms5MN30AfQZbSQInUSqVKJalqp8AJynjKaRzq8XcXeF/JP6O/BMw+GV3aO12Z'.
	'rzV8aJZJbHNkY0wp0xiBkSCTRKRCgUJB0La2tGyEndGCV874iFEFCCmcEvAZOt76UgyRiEUqQhELKYk2'.
	'+mRLNnp3uPjo5qVSBYolXkxVGsg4VbGQcT0xs53W9sW5ItyfJtZFw6/94EvWIKcbrj758U8BehN1bj04'.
	'G9UMqsa0HUXVdqudeMPFgVWMMxwmZm5nVJuve3mODHI6ZvruxrXbK6+7Zs3zeooSO5Q+0d9tSdX+n95T'.
	'uOP2Aja0XFSpCvv7K2OjwuYaYzAGJQmdmXfmFngGuVqUq3NKxZVadrLQPn9ZyfMzBp6WaV2vNh5dqroM'.
	'rcrpJBFJIqQgv9pzmHn2MjqPpQhjFcYmDFUrdSQrNO/+m292qu+++kMFlCZVfiDe1qcrmGcwEog+alk2'.
	'xTbGmCBKMaWEEcQIQcZAmiaBCDtxM9BtqdqplFob29T3yq1TzZets7Xm1gmgT5Mv/HL7fQ9vzTM8XCoE'.
	'gNJCrpzLMUYsBLYyodKVMJZhQHCUYyyLhY3p3nb86O7pS4dyTqlggDwzGiYg+tU9+/xbdxSQ4aWcUSbK'.
	'eY3+XOIRg0FrBcLD1ZjPthwaOB4qxAlpk4aM7qpMrl+W7Sv2Anp6KyvkL/ZN5qG1woqQSpQGoiAluT0h'.
	'DSNNMHDKlrMAQ8iN9CgYLDo6na7HhytVrSUyWGspkjikxZefuZwQQIQgQEjzMA07SZgqmWqpkcEnEdcI'.
	'EDdt3SNumR/07cxyl3RQyyXtMkI+IsTirgO6UP2lX//MA7pk9nz/kZv+5Ku/8+V3f+idlyMklMTkREyS'.
	'T3ziEycGCQs/318vScrbab0TTM01FufSlZeXzx3TZS2L1AxYqu9876FdsnagAszEQjIXydlwqiHOXO8R'.
	'4Ajh48/+BzNV8ZVv+T//qbRYjEj66P745Zc3zlqjkaQ2cmziOBTnrGiorz2yDO+ZoBRwseh4dq9DKcD+'.
	'WsciOuO5+kkqu1St75iaryXp8rzVa6soCqMoTqJaBwaFxsXk/jQMgwQHYbVem1uYOTA9s7NRm9fc3fvA'.
	't4cvee8oMjsXk6+NfziHByrRZBs6BhWNzimdESIjlJ1KO0l4FLE4ZkFAUs0SVY2ETKRKJU4kRKmoy+mL'.
	'8x9YaULlUM75MVrswUr7A//+/V/ce2/etm1Kdlebo2Nj55W9HhOXsSpT00vQsGWW2US6Xl0AAZlx3Ixj'.
	'lR2bErKz3smnkZ/1tXlabb+3U9t+3abcjfcYhVIfxwcaYvVg6+yspDHn0uLGcxD1QfWSzrBvFo2lFM7Y'.
	'lsfLFs8yvq8WxUlQzjoGnqr5nya37Tk8Ynd6aSqljIVAIgx4YfOsxLVKBinPaBnL6ZhaOGYqTiQoKbiJ'.
	'80RkifR025Fzjpj3gv3x7PaFzBnDNtIYY4znW51tk4+0w0Yj6DTC+lKjuhQ1rz7vTSUGdj5rc+vJL9OA'.
	'5CvfftD6t4OZXo6zGEnaGrbbtObhIE9xgdGioUWQA5e45vIz7/3ET6ySXxTe3T/+zpa9+rLXn89RKiSQ'.
	'x9I8Y/QJlr5VW7zvcGeMWUkUV6NofqE9NxWc/Tv9y9J6UuFOb5/repyQXK3yxjeXP/3RhXZUz5cthIkB'.
	'xWJdj0IMFs7Tx87efH98euDmH4uFyWrGT+oNfN9m9qV/gSGWnZ8uFIuen7E4J5QaAJ0kC7K5dNXl5O4t'.
	'bidQhWyKaQ+GHMF7p2tGyYH+gcd9pNFbDk9rJUeKdtlisYzbnSSMoqjTbLdrqXhI1h+Zy56W6kbzyM1h'.
	'4jc79TSKESGEMML9kuPW5scBzvvmoX8+WNnpAlrVd+mK5HJTa1EMPse2hQgiCCGEDUaAZLwQIjPIUxUm'.
	'UiYyikQai6glDvdnrx6oh00rdEwOHlP1R6YX/te/3tiuVTzHr7Ta945P/fEbf+u1ObnQqjr5gpvJ2LZN'.
	'OAEFWoqN1aqXs6sSS6mQwYAh7zBO0ZZqcK6e7hsZOZZ+HC93VGY7191X2D1Zt2nabqJ79uf/5NXiImod'.
	'XiwVc46fcx2HEQIAJhV13ZjYYOFJMxgJlTcCg0vxmdh+tBEbPXPasmFNKD4Bavq28elRJ8xjFSZSpLGM'.
	'24bwrdOh36n1Dgxk8nnXshk10zOL02n/sNyKjaUAIzAIW4gY0ap0alOpEEkadZbuKp3+e0GsLUopARtZ'.
	'E/NTxCShkEEcS60RQtCNgJ5KmhD/27/e739nH3t+bwIKTcfq5cUkniwZVOjtczIZx3YxBgDkzgYv2AgT'.
	'n33tvR/69+GxFefpFz763e9+rMD+/ktvISJWGB0zSceBPgnuPNRZ6ToyEe1AhJ14cqJWOLs8FFe0sgeW'.
	'jxSLBdd1AbAoFk7vzLzl/af/2Zt+ctElJcvmJoTiegjrdddzbd3tMphbDs+P3HmnMknTsMb8kfhnd+U+'.
	'+Qm0vie/c7x/5Yp8oei6DmOse6JSyoIoOfPTkxvXZQ9M0CjSFk8YsZBZTfDe2ZbLuZcvEYwRQnGUttP0'.
	'eX3cgAnSKEnTKGx3OpV2s1JrLE3u+3lu+aVF78XYaDVUPvyDa/y+c6zMKCaEIKJk6GdL2dxItSn/8eEP'.
	'UZpsHFr5GvHH0807fMcqFXsd1+G2TTBBCAFGNif1pUB5iUHjQuBExpFIYpHUoznLXb2qlovptJPZwBjn'.
	'GAPAkWrt41//MQRtZlmTlfrBQ9OX//YVvzvEdk1XhsbGioWSn3E5PbpqZYwqlsjMkW0tLTBWWhqwAIHH'.
	'SW/GeaAWvtCezZX6NcDxuL+nUYlu2GrPVCoWXtozF2yeLL76guyrcvavDg+uWlkoFDzPs6yjsZZSKi96'.
	'couTu4bTTo30REI7lgREKFqv+Y567Ftzw30Dxycke45UyrRVoDpMhRRJEoUmakzxEdyo9w/09A4O5rJZ'.
	'SinGuJjJ3X9gcb7lea0dEvmgU0BMyzCSqR68PI07WjPd/5JsOJuk/dyYbmu22qnaxGiNpRYEE9AKNAai'.
	'nhL3N9yy0/r3XezC3iSVUE3x6r4EL2TCND82VioXfT/DOQcAbYz2s7aMzrxy+QMfygAA5vis1Rc+fO2N'.
	'3/v9c950wTIRK4RQd43HQG9u3z8/ZHGQOkrSJE72768GMb/ibC0mzcrTVvb0lFzXZYwhhJQymi+7cqh+'.
	'wxXLH/3FxLrzi9U5eWaGpq1Q+6hrjrfUm8X7t1ARt6SJK9Xm3gMGTM97rnZ+cfvy008v9vZmfZ8QQgjp'.
	'ujNjjJTqTMcJp2YW86WxoK1sB1OWIuIhXTR66+TSCxxLWz4hxLZ53rG2LSYeR0OOSsI4Cmu1xny7NnNk'.
	'cmscTgyP3WBXHvB8r9x7QfHDU1u/9zZS2cFyZaVUGlVXXvVlZ3amvqxP3pnIF8In13x5YeIn/UOD/QMD'.
	'np/hlGKMEcaAECPY6qQPEJ7CrSamsUiDNOgkYSuqNnnjNebPkvjeZaefmctmHMcGjECJv7vpbtVaRIxP'.
	'zS1OzC3AfPWzV77g4M6Hl69Y19ffm81mu3reXbXWWjtO1nOS8X2PJihMhS0FIwQQ9gnOcbp5pvES2wMv'.
	'c2x+dFq0a3ceKE0vThNYOjBTGZ8J1KPr/vbTdMsjy1at7unrz+dzlmUd+7wxRmntuOvi2YOTcZJPFMQS'.
	'YwUcEQzLgeyba5c8h/tFhAxCCIxZbFZWuKIbsUiRyDQQSrRiK2tHg8uWdZXq6MMd+5wx/osggNmfGdIP'.
	'JgXAUpm48qAceLts7SgVvZ7ycN7niNLuVzDGUgIjRCBkE8vhPDGpBvWULaOfHDpg3bwPbShJoSBVaiK1'.
	'r6JWqzk8umJoaNj3PG4/oRLldiqzAIIwghFCDK+DVXe885YXbXt3Ngwx8U4A/WJ1EcXItSBMZZqqIIgX'.
	'K8HAuUVWCcojI6VSwfd9+th7EwIANoHCB9675kVfuZ9jCBFbud6BVhtTwggWAPPbDo7UF9uYpY1me6ne'.
	'Pvxw8QOf5pXpwZHBfKmc9f3uwRzPzWAMGeqdO9x7VyDaB9uZIESEADIhwoNx2AB+cHFpbNDBGBuAjf09'.
	'k3PzO47My4FcVrba7UbYnJud2xsHYX7VVTxq+Tl/aPlKqkVvW2Xe8K3pyS1BbZxyXhh+qVVbsgutnB38'.
	'4dv/9rLipTwctwfLPeWBYqFg23bXHhwFjYGHDpuZ+Js+UKEhTKNW3GpFzUn1wG87X0qr9w2vWlHI530/'.
	'wzkDQDduGd93aLzgeocXK9PVhtp35PlvvboYzNWL/T19Pfl8/phnO5pREYKN0dhZt2psZvfhtkA8iinG'.
	'jDGNkI1NQ+PJ2YVlqzyArmtWD+9bLO+drDusc3C68uiMqC2OXv1uy2n2OOVSb1+xWOCcHz9h3f2/tvD6'.
	'4eUzrYNLC6IviBBFgC0FkFHKFujRhcoG7iHLJgQt1AILJUzrWGmlpJSJSFqBJgC6p1x0XfeYD+k+Ppuz'.
	'M6Wh6KBLZAwIADRoEJKY2RtLy1437Lfz5azjuo7jUEoBwGjNKKaUYcAYYd/OUEKFNvhJNboDndnxbzy0'.
	'vD9ba7dlIkWS4owLA0lhgY4sK+YLHjyp/3AA4IdX3VB2S5hhBAgh5J6Wm9k+s33/wiV9WSkdxszjoN85'.
	'13E0ioVSQgmhqpUQND7rDEKaPN874LjOMePU3UeMkRbWpauGP33tRf/8+UPv+uCFPXGNeWXbsjBCD1ca'.
	'hX27JaOq1UqisDM1lQL0vesad25/cXhVNuN3D+bJnCQAZNmZ/qFibWLG6XTAZgCAKFVCZsNk0ucj+Tb2'.
	'c4SQBFB/b3mp0uwkkotmGLVqzbko6jAcI6vPJqJYLmbzedvm+SRxlxZ7+8ca+WVpEkPjUKGczfePIta6'.
	'7pqPJAv1qWhx0B3JZrO2bXcP5phMHlG3t75XMmEMfiyjMI06cXsm3HJ235/1twLekx/oH8zn84xRAGTS'.
	'9CcPbs1aVjNMllphHMUQhe+54vmLC5ODK0/P+H43JHjyqjEg5OU29OTumGuxjmKYOAYBANJAjDrQSQca'.
	'TVPIc8omktDZPaeNDqvNxd3TGiEGynvVOnu+1jM0ms9nn/YnMAD3ThvK76lVc42IMWzAYESFlIVQTDUh'.
	'LjVc1kMIXupEHEJpQCutZaqElEmUYg8xUsxmj0/WoevTCeR7+5uxsVgCiACA0UbrTDLzSObi9+fMdKFY'.
	'7H6LdONYozilFrU0IALYZTyXyWJltH4is3j20NwZ2cFm1NFNLaWSgQLcyRUvTpjZXFeoMidTE7cjEQnE'.
	'0MzWqb2/PDj1g20DxHPPKADgbpaCLcYhrk2HYYG5WBrDHwO9DBoNMQhUGCOUFFIvLQXIdzJKFoq9nudw'.
	'xp8wrosxllqrNPeBK1/49ivPa8w3m0s0P9jDOQeCFo/M9nTawvdFItNO0jkyTfGIW3BKNSf7NAfz2PEA'.
	'ULYq62/q60kO7DMdG1zAnBkpvU5rqe1XOs0+2yeEUEoxp30DAwcqi2mayDTutJYwthAmRoLl8pxrubZt'.
	'cUtzG1HuFMJSpy1Tiq2yZVmO4xBC2nFbeNDj93POKaVPWKNYkj+dv6eHzGHdk6pIadBSt5JFP/uC84NV'.
	'Kd6zauwc3/cZY90vbjowuzC7lMtYYb212GxDmsL6dWs9hEQ2k/GeYOOfuGoD/aWCuxTUOm3OCCCgmKZS'.
	'J3HcxLTWqPVmM0DJocWmNdcIE1GfrbenayRnIdKje4Ii9GRyWUqeYWORATNcKG0vNFuzbZ8howxhxEiN'.
	'2gGmzlS1ucovMMYkGJCpBFBKKyWUSmQaKyKp51LGnuLsEGZgpEgZxd2gXBvQYEBRMGBzyrnNOT1m4wxA'.
	'1st5lBqElFRSy3KmKLHSQE5s/ocr3YHN0U4lpejIqJ0IJWuPLO4Z+IwPGWbRhWRhGmYCEBSIB14RShbj'.
	'Q+ASi6R72oRjiRCAJhi1oK40j2RsSdWlmVAAWAjDuC1DponGItVKiXozdoeyeY7cXM7i/Cm3khAitJYy'.
	'By1wGcutsFzXdRgLAOuDU0ZJkcYiSkUapjK1TjvTtYjv+E8wFU9JzvA9D3JeHKUkigBjopWWIJt1lC0s'.
	'BG4xmzB+FGe5TFbMzwoVtoJKEiWEOcYYoQ0lxFBACGOCkTGu63LOPS+jte5GL8csum3MCfHM42k93HTk'.
	'CCNbuR6MdKSUQgoaYWWeLbzX/oewcfuK08/MZrNd5el+46EjkyINlcDtKI7iBJLUHl426FiLwul+7Jma'.
	'MAhBNjto8wcXE5sQgzBnMpGyE8QBtTphVEwlsSCebbtL7ThI6nsXkE2JIaTPX3naKJuNLccmBD8d6I86'.
	'UuaUBrPBpjlmpNZAbKolyGYAHVUfpiIMbc7BgEgCobACkEJIlcg0URQhIATjp2o8I8yRUUpLbcAgjJXB'.
	'RmlDUyGVAG3ghO6T0cqxHAoGIawQcShzbRs0AtD4BBTA0ng16sRx1GnOdIJG2JxpxHXZ97ZLdCQ00l5t'.
	'cB0+N5PzuE0wJYRigxGmhIDBlCBzdMlJKxlc9pLe3lZay5nB45pTc/UgmA1aJWpzFisdd6J2O+1lyuKu'.
	'ZVkEP/VWIoS6eS1lJa3U0ayUkgWpzORkGLZlotJ2O5hdkmK+uPFNDCPL5hjTZziYxzYGskO9nUYbS8FK'.
	'ClOiDKSLS1rxYEVZp4nWbvchrmO300TNHZqa3GkAY8Il4hQhMIgiQAh1Dwkh1M3xn5EscII8dLBzMPiu'.
	'b3CiF5I0DJKg3Wn8Iv72nw79SlTu7lu9vFgoeZ5HyONFj10HZxqdyGgzUWmABhBirLfoMEQtF2N8EqvG'.
	'y3syP9gjcFrTBnGCpDQL7U5Dh51CWSVxI+OkUzVSD+oLS9FUwyrauiP9c0ccrajtUkwI+XXEDUTKvjuP'.
	'NZuvWUYTTDWCZKGVqsCcn0viyJGZjOs3oiAmqcaOErFIUqGiVEQI86cpK6Iw1EomcaQ0UAAEhCdhRWbG'.
	'sEmf/AVCWbPTBIYBLGmSVhTUWm1CEBzHLdZaA8b7N08KEbfrSXOh1apGSwvVN2y6uu8slUuytu25jkuA'.
	'CtAAUiqllVJSpVEaSxFHidLaKKPBMEqoQNaSsZflKKXGGISAAkBi5NRUw6ZOh1IlTb3Rnppsjl5Y9Dhl'.
	'7JkMcxf3jDGt9bFDDaTq7N4DFlKsE8zO13fv1wDuwAAVijKbkJO5xYr0lYrbYoEXF0gcU0KkUsHEdDDX'.
	'MK+4KInjx6qiQBgkcTyz674UM8f2MSYYE0w5wkoDA3gixE8S8Qd2yJum/4WYQ02Zb0W1pmilQXLz7H/8'.
	'4ca/O0tk6r1uX89gLpd9QkR0z45HW/XGVA012yFjTCBS8vwoTS2HEkR+PegBDfSURBDuaTdiBZSQVOvD'.
	's9WpUFy5blAloYR8Z/dCfGBu6dFZmqHUdUQrcPKOzW3GCT0uhHgG6cvkH8gQfe+Mf7YBgxCY4EgtbMaZ'.
	'q1YLmQop+z1nuy6w6i+xP6ZUKqIgiZNkYat9xnuUasNjduT4Zy5UjgQLO2h2mZQSIYy40554MPOa39Pt'.
	'Kh2wn0BhoghVOg1lUWSI0qoWtueqc2CAUnpMpxBGraCxNFMzSjSmm825iDomBDViMkt3HbSzrpWTEQqP'.
	'U0JjDDLGcKDMkIy2tDEYg9aAMXDHdkeznudyTjHGR5tTEtjEgSXfKxDClVLNemvPrup5rxp0nAFCCPr1'.
	'p3VCFRlZvHroiPS5ok595+6o07EAZqYmT1eCM3ZSsEPgAdSDNt67j2hFFJJGNMYPhc1w5V99JG4s+Eo9'.
	'3kJuTE8deai86jJCGMaUYE4x/KcIwhJ+MnX9/sYdWRiqB3vmgtl6p7Z/cTKzbPjP/ffviX+4auy8QqFA'.
	'KX1C1rt/zyFIA8jmKCUWo4ISZRKLWgnGJzUIhwCo1Q6DPZPTygDmKIzRgbmZTePTn33Di0SUMKCV3TPi'.
	'1u1isGC7LlhUW5QCJphgBMic1J14GeChMuHuQyJPjARQKqq0a+Pzaf1C47tKG99Kisuev7Dr615RAcuI'.
	'uB1FYXvxsJrbkqx/MRKxsix6oh2c3/nTYGFCC6ZEB1sZFS6GdVi+4Wq/uhVb659oNBHtBG1ICMY0EWml'.
	'XRFJSgjXII6nH4eNdHbPou2Tyq4q5sZybQOirz8vWG9faSCXz2KM9VNS5I8LUzFCXXPcTQK75UdjgAIA'.
	'I3hiZrF3gCDMhDa1xfbUzLznXoCI0ehZcz45gsrktCImESpeXGAZL61aK8o+w0jBSV5WiByAJJ+pPjpu'.
	'EQ4iCRut6sOP1PXiulxWLc0cz3pv1evUpoS7BBOMmGEUIayV/o35wbOLS18/9Fcj9rpttQcPNifCNFYR'.
	'gIDx10w9uuPmgfVrCtm8ZVlP4QCNBsCcUsoYIwQYtbAtkTTaetp+45OknPUeevSAMbgVJwvNcKnRhG0H'.
	'mGWpTmgDVB6dcEBZvo0ZBo6VwzClgAxFTMPJTh4Rh1Um59RgTgsjW3F7obU4taO/+ap4xUDWaAH0/EH3'.
	'O6e/J731Gr7sZRIgblVj7dZ/9CfLnn9wLK1go+C4pPOhGHb/+3vyRUfM7TMIgT7cvktsuP6H7uKEX8pz'.
	'23mCi3MwXWwvxYyA4YlOlJRZP4uQBn1i0RKpQ1sn/IyDMHFcBzNKwQ0WOn6vm/H9bgH9WZD4ETrez1MA'.
	'IJQePjA/MOBgQKnEhycWAdqdtrQ8S8QKnuXYIgKYOfyIAAd7Bc4datsd0GMDvY7ninbjJKc0HICoETan'.
	't3ONRKsllZQ68ZzVKo2MMcc/hNketzOMMEwwxgQhC/1nGOoJXPC19UVSfmDh3sWobQFgBKoDt31wemLL'.
	'bd7qgd5iv+s57IkuywAgsChgsDhFhHBKwbalkRQReDZGo52odMfeTQiDQkAIYID1q0gcC6MVALE4pz51'.
	'OcYIGDYeB6ylNM/qjJiF5ptz8XjORNIolWjBoHjxhac35uu9Thkh4tHkkrMuurP2SXnT29jysyXKhalq'.
	'7zp079+/qvevb1l3AuMNvvIGz12AtMAxQarZaG6H86+/oXf4JZnFe7Pls+wn+3ZOFqtVlPcZVghhzhgH'.
	'jjSC4yw9AGgJB+LJDZnVjutQTgnFfZAff2h61VX9pFugwL/5/BMFAM8yM4vtwweXNCLNigxkAmBPTQfc'.
	'YSLQz9LSGw8Qor1G1qjjUHr0/Cvjh3ps26iTfRQGqGzaTABU0MGUYNviUdvdsBYlKTqRfo2R5lYGE0YI'.
	'xpgaygkx2qjfbDvW/YBqmA4AAB5ZSURBVMPYzHSllqlJrTOU29hemm195Pe/umKOTgzoNQMjuVz2KZsM'.
	'ANA/ODh/6LBFGSaEUOJ79vRiQ3MLR+nJg3Lb4UMgDcWIcGpxHsaC92aVFAQTBOCMlsiuWcoYoggYgoIb'.
	'TTUwphIkPukhuKXdlRYc8ZIVBCPgBAfSK5ViLJEAAwZjLIGtsCriorc80LP28Gefp48AWQt4xF/8wX/8'.
	'y4H8JR++b2z0dETgwK7Nt338wmIHvDMKOqiLeeBj/EU/fMjRPfzIz4ZOPy/j+08u1FJjAogpLlDGCMLY'.
	'AMVEEQ3H3fRkwBSHS0XwqU0Zp5RRzEih5D5456HVvz+iW1JqReE3v/ISA8BQ0QeRju9dmplrhmlsM9Lf'.
	'k7/vF/s6BhMslH42E7sGygB9L7gEICIWR5wBZW5pqHlkGgexgJNVIQOAIsGBEMshtkU9l4JoDfQmtQV2'.
	'QkcQMKaMOZwyShkmFGECmgL8JkH9W772+vG7JpgPgMC3/YyVXWq3zr/sFe9Tb92X3Dm6bHU+l3uGcvvG'.
	'NSuhWuecUooZIUXPDoJortFhCCl1skooYg2DRY9znzGfUynTc1eO4rQDmAPE+QtWaBEih2COsUWYz1Sl'.
	'Mzm9YHMstTzZWcc0zUIPsSniGHGimnHPqiGTYUgerXRRSrWdXZWpvGrNaZdfa8Y+9XXXcTmnxfNLXqt5'.
	'52+dce356B/XoE3vubCUAnJAtuomPzb43usu/ERAZquZ5paV688tFIqO4zx5r7QBG9kUY3o0zKbc5kgZ'.
	'jR5/eaVkDmc9lMGAiUUJw5QSa8RK90Vt5WkTgv5PTZFT0CaDs0Mbl81snSz2ecgQwlC+19279cihebMh'.
	'J5RQhp9spQ8Q4gD5y148f9d3MaFAABBYPfmp+7d6ltM2qVbqZOqGVYDO3t2e3Y8ZxgRjziQkwy+60BMK'.
	'u+4J/AXAhDJEKMIIIQKYot9o3u/27T+74as3wfPAopRzZmGrHlWgCN86/ae7Dn93ZP3Zxcc6i0+npK84'.
	'b93PvqGPdljBcMYmJ44sCDJkpdqcbLV09/Q8ZHxOMKPEJhSa0dkrRpiWijIP0mWvOWfrR79hUwQYIYwB'.
	'GwvI0lSs+oxRJ/UTCUDtoXELegAZIIhwEqjGyivOaM41iq6N8NGky+IceG+UBzVed/rWcq8QV2ZgHrIr'.
	'sXXlaLXeLvSsLvSN5kfO6ll+oWZ9hvqoOWvtv2VoaKjUtzKXy3VbE09hIIzBCBNMKaYEY4zApgxhYrR6'.
	'nNtgMACc/4q1B+6cy/YajAEhYD6HbbOHt9bWbMwpoY1lfuP7QnDXq/zONWcBtChDhABBmFDMUObzn73D'.
	'K9lGPUtjD3DeH7+12U3CKQWMMLfSdLHx0DaTzaXipO4g/+WefSY5QgpFRBDmDJQktPesq19tlqr0sQ7o'.
	'Y1qGETnKr0IABrSBZx3bHBqPXvapV8KFYFNAGBkDqepEHbj1tYtz4z8vjCzrK5cdx3lCYDM5cWTkLwcm'.
	'pttgMKj4zRefC54vlSQYIQCCMQj0y70HPT+jZKxPYg+l1p2Dh3KlPCOYU8yxBoddvn6k1oosx4WYDI5l'.
	'lkZyJk0NByCAGNGBsqaTlou0OqmfaAMs3L2LFgsIIUyxQUZAuvZtFzUPzGay2WPZ4TzAdbfc/JdvGrzp'.
	'muL4+5+Xtme8vLP+2vG175s745o73vRX82/88/te8QdfP/+SP+j3yz2kOaQPr+5na846Z2RsRalU6jJt'.
	'ntJAaABKDOk2lAhlmCLMgjA8wXsTAqD1OeU0biMLA0WIGMAoszq/5eO/qhVyNO5IoX7z8AYhBCDe+8eX'.
	'ABCjDGVACCCAtRuzN33poYf2L/ieEFI9i8BepmfmnOK5rxRHtiFGEcaAsQXZvV+8vq93MAlCfRK+fumX'.
	'D+QAwLIQY8jirUM7N37gnQGWDqHcto9HHucORsZ0I0EDxmB4timOgJX/x4Vl4HVHexCmQKqzyYde/ZUV'.
	's2HajwcGh33/KfhCa68dnb59/vbpnYAgiEQx61/yqssWxo8wjAgCA2bZit4vfPuWarOJFciTQOR/bNkF'.
	'c0uubXNGHc5mGp3nXfy8tUwI27ItLjU+g5de/1dvrD54iDCKMAIEeE2u8Z37mo4L8UmBfvvcnA5naM7B'.
	'GBNGwgOtC573wsYAeMgmlBDGAGDrXPru89A977jKWpgrr3bdM0qwBEOfDOGwzs5tWsmmS9HDrLqdNffa'.
	'aqGnaI8uGxpZsXp4bGVPuaeQz3etw9MnmggQxQZjhAnGGBPX4hkVpXC89zYAsPEdl6RgkDGIGoQRAkMH'.
	'HfWrQz/72t64hzERa/3sss0TYnqTohV24WW/f9mh8VluEYwNIoZSPDI08MqXfqtKpWMJLU72r0QoQ0oA'.
	'z//+v9VAI6UBE6O1f/b67d+8bqrZlDbVUj7zg0KAqb/+nAN9mGLKuIkSH/zyp95X37y1r3/gGNnzKD90'.
	'aQoz1+ju/YhaayGVwIBOfj/WfP5iCMAlSIFmmHFCl+qdc194xQf4H4yrB0eXrczn8k8O5X90/+3xAqBL'.
	'YMjrhcU0VQpE+PX3vgWUEkpSQhBg37I6UwsPT1ddnSolzK87pHf9zb/AaSs4oZwQglG7nfzpay+tzs3n'.
	'S72UYsZ5HAdnv/Xl3sr+tBEjRhBCxOdo+5GDv5wOclinif51Z7Tp724pgSYWRZQAwiKdOvOf3jz/8L6e'.
	'Yp/rugQhALjjlv9DxmHspT7vzSJEjJLIgeDOj4eZPjpwgTv0Am/4/Nzys0przhhau3505crBoeH+/v5s'.
	'Nuu6LqG/puNuNBBkMMEEIYIwRcRmzmnLB0KRPh6XIgRSXbR8uO+VZ4rxAFsYiEEYMKDCxSOb3379Tx6Z'.
	'1R7BQvxaSG6V9RcPvH/H7CIAaKUfB72mBHT4zX+7CsAKlgTjhGDAgPqX2c1Zffq6LzZBEM4QMk+Xhv7D'.
	'j+5D7rsnWi0AY5DRSXzxaN/Ixz7XHn8AU4YwwoRmwP/+lX/ULPXSNFDP6Jtu3fSQmN5Bx4YxpZrhyqGH'.
	'Lrv7B9P7DpWzZT+T6fIdjn04qncwtY0xSiuttdEGA0NIcXpS9v6PbvzC/js3WX2AEOGYM8QrUQB5+O7p'.
	'P9tV/d7ImtMLpdKT2ZfNBXjt9S9bs7qfE6CpbrQWtdKhMCt6M//rL/5k4sFtmCBCMEbYXd7/4W//nJbL'.
	'zVrrmS/x6lTnF7fsHuwpMEocTnftmfif7/jdVZ05lc1mMhnbtjHBFuah6bzqh+9r3rcfDCBAAIhsHJp9'.
	'3zcOe1kkEy2faWM3Axz6wtf90TMRxsRiya4dq9//vomeKBfSbDl3zJq8/OqPQi+oTgcZMGBAG9ZbCG77'.
	'lLjt4sbOv2/tu3bpoX9e2vLlXT/69OYbP/6TG/7iR9/40+9d9/7bf/DFTXd9b2pu5tckfQwIppQQirsJ'.
	'KuWEKqUQOqH4oBDJQ/yya185X2sjpYGDwQawppSObRz78TlfuPYnj7asZ4rqNcAXf3T/x9i7a/OP2AMZ'.
	'o2KljiOcEYxEbPXY6sYH3/OGCz9zVmktsxkYYwyc87zS+MO1QvYT19305ndcfs6T54hnTON1r//6Qzff'.
	'BrD3cPXjo1lLKsAIl8PWyz79wZs23Zf+8mbrgstAA994tnXnd+649jXlP3pTT9TWjzH6nyB3Atxx8UUj'.
	'7hixbUCosvPeV33uSwtrR8ym7SPnnM0t3mXRPBaYQE+W68ACrQEQIGMMMTrU1jKcVizr1/Qvbt02++Uv'.
	'fgCdC4xwjBDDDBkNCdz7+qXmwduH164Y6x9ysj7gE56zaWfzsm+NFvrAI0Uv2+YpbYStfI+PCYUo/qtX'.
	'X/Cr8avv/v6Pznre2cbAilJu190P//13bv3wm36rVq/lcgXyNN3Zobd/HMZGXM45Qdv3T736Da975ypr'.
	'er6y+syNjm13KXGUUadm+Nqh86/7o51/+C/5V52jDeCMbd8/efc7v9F/3f9YGTUN9tFTBdNVgOtP+6Nh'.
	'8GnGRhjktr3l11zV87GznF/tGTh9g+t4R02JVmdlyMu/fNe9V7949DKglGNktNKJlVOLe6P5fZOUp6kJ'.
	'grjTgHYFRAAYgQbQBtoaLAuwgeF1zFn1ijOuvvaqcwYAtDmuW1zmtuf6XbhTQikmMg0eWGi/KOO7VOPH'.
	'PDQmWIX8+SOZ3f/0iukP3F5+6YihYDQY0DTLV5w/tuW3rx2/8pwrPvfyDaP9Q3DC1SPTED2w+dBPP/ST'.
	'9r078oCv+NLfjCBZrcR+LkOBAgDqYl8orYLQzsafvnn/n1/1xTM3jGULjtQGGzAIdRrRrp1TfWv7X/rq'.
	'My594Wgh72uDlhZr37p+x/0/2mJBJoGlqz/65m995pKlOenns5ZlKSVUFGzJFn95xe/SW79pnfcSwFR1'.
	'wubuXw3e8JPXXfPK/qPaeALuHwS4DvWvhpideVYyt9iu7H3lF65D//Oaynd/un7DGT0DA77nHR/e7B4/'.
	'dMuNf5LNDmLKu3OWWmsjjrAVbx5d89LR3tzqMjlKzXgS8Ua1gP4egmEoeA4CQjGhmM+mS5+4+F8/XnrH'.
	'vdHe0bE1jo8ZBqWhGuuFTu1He773H/u+sXfugbEBr8xXSx1XUXD9RfcM+e3S2Ijv+cYY3GmC7b3y8zfc'.
	'dvNNZ5xzFsEoEnLfXZu//qVPvfWKSwG0Ud1M6oSX2fiuj2278/7Vl5wr4/TwwdnXXfO6v3n5hsO7x4fW'.
	'nT7Q15PL5R5roRulVFBv7MjD9n+6Hz5xPX3+Ws2witPmHZvph9985d+9fc1TZuoA173xb+HGm7Mbz9Nh'.
	'KsZ39lz1lt5rX0J/8cja9Wf3D/Z7nmfbdpfshURQtTJfu/Z/T/79X/a9cIBTTJBGSGsljJZSGdCgAQMi'.
	'mBJEWPcMjTRaC5HKVCgdtZM5mK7Ciz737Xde+SamYiA2ADQb8LUHfvbl2z65rm89xaxbuBQ6Em72gxf/'.
	'2cqs27+8e2WN1ga0NjTpHPCib330oc4/3Jt5yRAgZIQy2mhjQOt4X2dxvm6f1188dzjT56UiIdRq7K8s'.
	'3j8hDtd6RvOViea5/3TNa9+zSuyoFvr7crkc59wYeRT0xhghpOy0nWL67U2zf/jq7/tKrDurhDnRxiAD'.
	'SpuolU7ua1ejSMsQgFBuD/d79YW4rfSff/XNf/bWngOP1Au9A6Vy0bYsbYyMorDdeLRv8JFP/XPz4x9x'.
	'3DysP12LtLX9HvJbv3felz57xkhpGIAABAB7ALZ+5+d7r37zAGVmbGVt/6Y+d/SKh2+fsGW46ZF1Z23o'.
	'7+/3fd+yLADYO777tlu/nAbNdmtPX99GRDlGuEs6AoOMUmk8FXXC0BpuCXzBxW+85mUvk1IQckIywD7U'.
	'KxtLA/15gxBB2CBFMVJa2w5Frp+CEVEiU40kEjqpR00AsH1YYQ9k2QBGGCOijZyF+S+ff8fqnCqMDmf9'.
	'DEIoThLdqlgG/+kduz73tesz+ezyQkZpvefurZdd+dIbP/L2Qrnv+BB3+57dZ7/vM7DYGt0wNjFZA5f9'.
	'7Uf++Les1sTMwvDq0wYG+7PZ7PFTZlprIYSuLe3sZdt+fCD+wPVYSLW2bMB07toenHvauf/6rjM2rlgJ'.
	'nAEkAAch2b55/wOv/kx+Yd7asFrsOAIAY1/5qH9VP/r5g6vWbOgZ7M9ms92JgseQIFDQ2Gf1/vAf39X5'.
	'9nWF5y2nhCIwYIzu/utexGoQYIKQQUcNijGqm1gZAxghDqp15L6Fy77ywOsvvvAdX/38Iwfvj6IaWM01'.
	'+fUUY0QQxYQQxjFNZTorpsv2mkqz8/oL3/A/Xn65lBIhpKUS7fr+srzj8zvn//evsmtzuNc2utt7NICQ'.
	'UVp1pJyPdKK0AYOBeBgyJHykpk8bfPHXXr1mfRQ93Mn39/b09HiexxjTWqBj6anWRkiRNJsILx3Bzqf/'.
	'+uG7v7OdG9TTa3lZShgGjACMMVpGulmPK5VIYn7xazf+6SfP7U9mpg6I8sBAf39/N3kHACmVFEmwtDQ5'.
	'UN6/b2n6L/5a/vAHiBLUUxK1ehTPiVUXkGJZioQhqrfe40LE+ZBOQyj3v/BTH8m8+20HfnkHbQSrTzut'.
	'UCh0p0u7LvgH3/virT/95OlnvoxxTxNqUQsRQNQFGWljjAEtVNSZUXG1s7h90n7RJz95Uz+NgTxe63x4'.
	'IjrvbS6cDcCBWGAM9Gb6bMvjGEmdBqrtYJsgjDDVRjPAnHoM2QjRVLabssEQqkcLUumWgR+/dM95BeoO'.
	'92Z8H2OstE4SEdXmcwz/Yqrx8a9+75HZStqos3xOpUqP7x8858xlg4U4FNxmmzfvgrk2rB6AOMkO9l56'.
	'7umfeP1vm/2PzAm9as3aYrGYyWROHM8DAFBaJ0kiG7VJ3Dkg+ezn706+v8k0O9DjC62jQwfjlavwQEEL'.
	'iQlROyft9oJdGtHNNmQy+Te+qO9jL8JLB+2D7dF160r9vZnHpsifoFc0aR4ivXfefF39jnczb6Vu7zcG'.
	'jCFxpKQCg4A6Lue+lSlRZuNuudhIc9QOIwPEYMu0xkn+tPM++MCbvvQCW7SHM6uybjGXKSJKLMww5dok'.
	'BKxEdMKkE8r2wca+ZdlLf/7Ov05QRBkHhGSaimbliNPathfGP3mvemSBlRkuWtgl0J1aMQgho6VSHSEr'.
	'Il2I1GBx3QcuOu2aHN43CzW7PNzXUy53EY8xPgH03cl5qVTUDpLmLPhid53dc+/Cw5vmm/Pt+lIbA8IE'.
	'x5HIlZzy8vJZ5/VfdkVp1O3M7W4Zyy/3DRTy2W5pr4stY4zWOkmStFlbiDqLfQOLU836z36R3Hu/nJkV'.
	'E0fM4jRACmAAKF25jq45zT1jvXf5i2m/U8jy9uZdPf29g8PDuVzO87xjdV9jtBA4EVIm6YO7x1sKM0OF'.
	'iIyWBiEAgrqJEuPIYgTRgXxmxAqdTM5x3cfz0QTuO7yopJShSKUQIvU01QowxpRgyizOHx/fVgCglNEa'.
	'tO6ItAMRKJNQqUBpoMtFMDha7ukdcB/rmkkphdJBo65b9TAMBLV2LrV+sGXPvrmlvUcmW3NViDQQDFoU'.
	'Vg+ft3psw+jQmy/eOMz1zMSRSieyisX+gcFiPu953tPV/rTWaSriVqtWn5vJ08U2aW+ZDm/bJ6ercnZe'.
	'TFRQYoxBYBI81mOtGiGrBkovOzN7fj9KFuSu6ZJb7F02nD/uJ54yJdwewsO3f2/+5rfTdqd05aeTZide'.
	'ml69csy2eFKfjONO1Ko05g4H9RlAYOcKVm4UgQKtDYBB2BgCCMuFXRv+cnKNMm0OSRKFQYTjOEpTLaRR'.
	'KgFNMZYYEYu5zAXGB7M0Zxsrn3VshxCijRGpjOu1xWR+xiWTD4cLP5sWB+vRRA3aEggyRoMCXHCdoay1'.
	'uth3RX/5LMJrDTxrMsVyT29PNpe1LOsYLJ8I+qNarlQcJUGzkbbnJRLSJ/NV1ZaIAiBsUk1yLMn5Rjbj'.
	'eEmB7eaKPcVizvf9bhPu+NJKNwaVUiZhHFSX6nGn4blJoRw22kZoSl3EEUVYKc0cZnkMR7GYPIxbAWFW'.
	'T7mczecdx7Ft+/jenlIqTdMwjGrVukhjEXWCKCUIKS0BMAbQBJBClDFKsGXZjm87/tECyOPaqCQRrDO/'.
	'MLc03wo7oJQG0NhQQjDGrHu7GcZdmg/SRhsw2CihlNRYaZFKLTVGiDk2L7rFnv5Mxjv+z0F2Vx3FcafZ'.
	'bFYqHInl5XxkQCKL2ZxjiigxSmIE3OgkaOw+vFAX0nO9bLF4DO5P2MwnloaVUkqJNG1X6vV2pYGjqJSP'.
	'EqVCTBgnnCGEsEGIguMigiIxMQ/11CJ2rqcnk8u6juO6bndjn4x4DXD9fVt3fO7iXBRbDNZ8bimZjqio'.
	'GgOlfA4Zw/wc6JQSyTizuG4euHNp/NbD999olUaoNwRdjTMEDBWVXSve99BKseBlMyChEweJSLTUXUYK'.
	'BSTBEIMJBoSZ53FquW4+77nuMSqr0lqkaRrFrcpCRdVbLjQ7OulQRAkySFODBAgUWVxwomAudATz86V8'.
	'oZDLZ7sG/vgey1OA/mhVRAgpZZyKuNPptBoqDdMkTlPRvW5JS6Q5sy3Pz/m262Y8j3Pe9R1PZ5akUlKI'.
	'JEmiTitutkKlNNIGDEYYYYwN0kqBkoAIt2zmeRnX5ZbVXfYTBu201kqpJEmSJJZSHZ1fQQh1U36lu45W'.
	'K91tyxNCbNvulh27r9dVxSRN4jhOpdBSAcbo6MsgbboznhohdJSui0ErgxB0b8LRUhvUjaIMpdS2bM75'.
	'/9feucTIUZxx/Pvq1T3dMzuPfU28i9fGNjYYC9vyIRcUQV6HHAJCICG4GIFA4sgBoRwQJ98QF4szEUKI'.
	'JKeIoMiJEjmRxUaWkTAQWZENLGLxYz0zO7vT09314lDrznjXswzYIWtRv8OeVlXV9f27prrq/1W5pq5r'.
	'p5snpHneW15tr3byfgpWE2sQ3X+iBjDGIqWlUhiVSqUoCgeeehQjoYuUlDJZXUm7K2meZyYHAgQAkRE0'.
	'xoBRgEBEwMO4HIRBGIZBEHDOh7lzT/fgty/82Jyc33akYa+0dr/4lxrfR67O1yamy9VIKdRWa6W1ttJY'.
	'KbMsNaL8o9L4TKOq/vnqA/1kgdcPorUGKFhmrp6def70HdniRHNaBIHVGtDtS9G1FXBEYxQA0UoRRMqY'.
	'64TBFQtrbS6lzPM0zZJ2K0lXk6zX76e5MtYoABCEE8oEi+LqWFQuh6EolUpD9DNE9EXMtNZSSqm0ksqC'.
	'VUohICByThljjFI3zx4lHa4Y9ZUyUmZaayVlsZmCiIQwyqlgzDnSnfd/k6LcBmRhODHGUEq11oMtcU+3'.
	'sYVu6uUWbl0J1hhyfXWDPbNuLCy+/l2Zw8ZLtw6hi26UUmsjldulWjv5CAkRnDvrlct1KM4kGpHBSCmH'.
	'1IXP1PkzKOdFxzoRDKvij5/l7zwczJUhvnu3lYmojB984UN+7k/NnbvqtXpxRIBdw2itlZL9pN9ut1YT'.
	'2tl56IvXjiBawuuAFGxOTTb56Nt7aac5tz2KomH5a2tRsNblJG1MKXb1aa2l1HmeGWOkkmCsUhoBCKMA'.
	'wAUvNDnMGmiMZJskQ7m6hRCuW4tgF6f2uEJH9P04+57LUzQmcAW6TG2nHmPMiCF3Rd3YLM75iI3ZfPLw'.
	'zZvaA6/rZhURwq51o7VWawWAxmhEUrwz7pG/s0H8hpEa7NiiinW5FBv5j4TfPx7cOQPBtj0AaPuL0aHH'.
	'TOtird5o1Mar1bFBLRaJDVrrSkWPT0yZtPPlcmfp/pd77x4lE5OEEtM6Fxx+lmW5qFE3HfjOLjH39hJC'.
	'GKNhKAZHveIBrbXF78MmFbFRUk5uUh+3XHBbgW8VvCIMZO3qCf4/as9Ndux7bx0f60B4YJc1GilHZJlM'.
	'tbGVSqVUCodJ1g1AxhgsBUGNz/8bwFokBIwmBsQ9T4adT8XsXYh4S+5RQySIcDNJJP76Hc/gx3EeRwBI'.
	'0NnZgga7/HFlbNIirrM83fAHBxnXHDrn/06iOgAxF/8W/fIYtlca9TiKyzcj01uLF73nvwRhTZFrcyGC'.
	'PJ66dOYf+ye7Ot4ts2QUx+F7H35lTh0n8Q5z8UT8wCuk8mBdf1qfarq5jRe9Z8tx+BdHuwRMtoQ0IIQS'.
	'SuM7tr/2zPTMrIByAxFhePp5BvD2+xfOHdsfzt5nvzgZ//xYuPO5seX57bv31Wp1zsX3c0fsSBMkf5+e'.
	'59qyRq6JeOPEyU9e+knzwAQdmyOISFjaXehfXrzz6Jv7H3zi7iqsu1ZKA3wF8MHHlz569xV+6vXStnFK'.
	'Jyef+gN2o6hzenbXvslms7rhvN7/62NKL3pPoQZjst5VrPx5/sz51+8vdZNgdoaVtyMPrMmTC6fyypw4'.
	'8OupvT8No3FrUsKCrNtuL7yfnPtr6dK8aM5QRSs/+83E4afh/JkIrk7NzFVr9Wp1bKOTwovesyVwVjPd'.
	'X17qhWeTaOGDN+TZ39mFf1HOkHJgASBYYFYu2bQFiEAECXYQ1rJZP9zxUHzokcaeX0W9ll36KCqX61PT'.
	'tWq1uNNg68xtvOg91+seIEtTmfVXrlxezCpXcp5gv7f4ie1eIHJZJ1cIE2AYWGu0pFEDK9ui6T1x894I'.
	'aNhrYfI5Yzyu1iqVMeekGHZcihe9Z0tpwkqZSy2z7srKcqe9vNKVLKdlA0KBpkgZQ0IZIUTwkOo+zdvM'.
	'9CihTPAwLgdBHMch53ydc9OL3rPV5zluvzPLMqWU7CdplmRpZpQyFrXWRDBqERCQUMYZYUIIIcIwFMWh'.
	'hnQLv9Ve9J7h0i/U7/4aa5yNT2tdGHistc4QekPDjBe953ZVv7PxrDtFFK75Yba+0NeJnvmgejanMA7d'.
	'XuLeBL8j6/nB4UXv8aL3eLzoPR4veo/nNv8w90uWHj/Sezxe9B6PF73H40Xv8dxOfA0QjFL5kb5yfAAA'.
	'AABJRU5ErkJggg=='.
	'');
}/*
	end function zoogleimg()
*/

/*
	function:pay_for_living()
*/
	function pay_for_living () {

/*	maybe get corzoogle to email this to the webmaster every April 1st..
	my birthday */

echo <<<ERE

	LICENSE
	--------

	WARRANTY:

	This software comes "as-is", and while designed to be as fast and secure 
	and fit as possible for its intended purpose, it comes with no warranty, 
	expressed or implied. The security of this software, like any web 
	application, is dependant on the underlying security of the web server on 
	which it runs, and all humans using corzoogle for "live" web sites must 
	ensure they have taken the time to familiarise themselves with the 
	security options available in corzoogle's  "preferences" section.

	Under no circumstances can you legally remove the copyright notice in the 
	results page


	PERSONAL USE:

	corzoogle is FREE for personal use. please display the corzoogle logo, and 
	at least a link to corz.org if you use it for free, fair's fair. most of 
	my work in the real world is for free too, which makes buying stuff like 
	food occasionally challenging. if you dig corzoogle (dig, geddit) and want 
	to do something real, or if you're just well-heeled, please don't hold 
	back from sending..

		money; any hardware, especially good hard drives; hardy boots, 
		size-UK9-and-1/2-ish; quantities of Tahini; blank DVD's/CD's; inkjet 
		ink (good stuff); mung-beans in any quantity; a copy of the best 
		photograph you've ever taken; fiction or poetry/prose that you've 
		written yourself (in fact anything you've created); free registration 
		for a piece of your own software; interesting stones that you've 
		found; guitar strings; any musical instument at all; large pieces of 
		paper or card in any quantity; fine spirits; yeast extract (any brand 
		except Vegemite); a solid piece of shiny metal with a classic or 
		interesting shape (the heavier the better); paint of any type or 
		colour; RG13 watch batteries; interesting recipes for bean-sprouts; 
		essential oils; a phototron unit; large expanses of plain fabrics; 
		original jazz recordings; a copy of "amusements for invalids" or "the 
		book of leatherwork" by Mary Woodman, published by Foulsham's utility 
		library when books cost two shillings and sixpence - I've just got to 
		see the book that is advertised "The Book Of Leatherwork: The latest 
		fascinating and profitable pastime. How to make dainty and useful 
		articles for yourself and your friends, or for sale to buyers of 
		leatherwork." wow! ; erm.. BeachText "Gear" (because I never did get 
		any yet of those); black ink of any type or quality; perfectly 
		symetrical pieces of wood (preferably hardwood); any "Meg and Mog" 
		book; very large lumps of blu-tack; original instructions for a toy 
		that have been translated from a far eastern language, if you 
		personaly find them very funny; a set of needle-files; a large bottle 
		of Lea&Perrins, a G5 mac; och, there's loads more stuff I could be 
		doing with. as a smart shareware author once wrote "send either one 
		dollar or one million dollars, whichever you can afford" to:

			corz@corz.org	(mail me if you want to send something physical)


	COMMERCIAL USE:

	If you use corzoogle on a commercial site or company archive, please go 
	here..

		http://corz.org/corzoogle/buy.php

ERE;
}/*
	end function  pay_for_living()
*/
echo '
<SCRIPT TYPE="text/javascript">
<!--
if (top == self)
   top.location="./";
//-->
</SCRIPT>
</body>
</html>
';
?>