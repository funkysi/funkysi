<?php

// dynamic mister wong link generator
$wong_tld = '.com';

if(WPLANG == 'de_DE')
	$wong_tld = '.de';

elseif(WPLANG == 'zh_CN' || WPLANG == 'zh_HK' || WPLANG == 'zh_TW')
	$wong_tld = '.cn';

elseif(WPLANG == 'es_CL'  || WPLANG == 'es_ES' || WPLANG == 'es_PE' || WPLANG == 'es_VE')
	$wong_tld = '.es';

elseif(WPLANG == 'fr_FR' || WPLANG == 'fr_BE')
	$wong_tld = '.fr';

elseif(WPLANG =='ru_RU' || WPLANG == 'ru_MA')
	$wong_tld = '.ru';

$checkthis_text = __('Check this box to include %s in your bookmarking menu', 'shrsb');

// array of bookmarks
$shrsb_bookmarks_data=array(
	'shr-scriptstyle'=>array(
		'id' => 278,
		'check'=>sprintf($checkthis_text, 'Script &amp; Style'),
		'share'=>__('Submit this to ', 'shrsb').'Script &amp; Style',
		'baseUrl'=>'http://scriptandstyle.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'shr-blinklist'=>array(
		'id'=>48,
		'check'=>sprintf($checkthis_text, 'Blinklist'),
		'share'=>__('Share this on ', 'shrsb').'Blinklist',
		'baseUrl'=>'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Url=PERMALINK&amp;Title=TITLE',
	),
	'shr-delicious'=>array(
		'id'=>2,
		'check'=>sprintf($checkthis_text,'Delicious'),
		'share'=>__('Share this on ', 'shrsb').'del.icio.us',
		'baseUrl'=>'http://delicious.com/post?url=PERMALINK&amp;title=TITLE',
	),
	'shr-digg'=>array(
		'id'=>3,
		'check'=>sprintf($checkthis_text,'Digg'),
		'share'=>__('Digg this!', 'shrsb'),
		'baseUrl'=>'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE',
	),
	'shr-diigo'=>array(
		'id'=>24,
		'check'=>sprintf($checkthis_text,'Diigo'),
		'share'=>__('Post this on ', 'shrsb').'Diigo',
		'baseUrl'=>'http://www.diigo.com/post?url=PERMALINK&amp;title=TITLE&amp;desc=SEXY_TEASER',
	),
	'shr-reddit'=>array(
		'id'=>40,
		'check'=>sprintf($checkthis_text,'Reddit'),
		'share'=>__('Share this on ', 'shrsb').'Reddit',
		'baseUrl'=>'http://reddit.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'shr-yahoobuzz'=>array(
		'id'=>73,
		'check'=>sprintf($checkthis_text,'Yahoo! Buzz'),
		'share'=>__('Buzz up!', 'shrsb'),
		'baseUrl'=>'http://buzz.yahoo.com/submit/?submitUrl=PERMALINK&amp;submitHeadline=TITLE&amp;submitSummary=YAHOOTEASER&amp;submitCategory=YAHOOCATEGORY&amp;submitAssetType=YAHOOMEDIATYPE',
	),
	'shr-stumbleupon'=>array(
		'id'=>38,
		'check'=>sprintf($checkthis_text,'Stumbleupon'),
		'share'=>__('Stumble upon something good? Share it on StumbleUpon', 'shrsb'),
		'baseUrl'=>'http://www.stumbleupon.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'shr-technorati'=>array(
		'id'=>10,
		'check'=>sprintf($checkthis_text,'Technorati'),
		'share'=>__('Share this on ', 'shrsb').'Technorati',
		'baseUrl'=>'http://technorati.com/faves?add=PERMALINK',
	),
	'shr-mixx'=>array(
		'id'=>4,
		'check'=>sprintf($checkthis_text,'Mixx'),
		'share'=>__('Share this on ', 'shrsb').'Mixx',
		'baseUrl'=>'http://www.mixx.com/submit?page_url=PERMALINK&amp;title=TITLE',
	),
	'shr-myspace'=>array(
		'id'=>39,
		'check'=>sprintf($checkthis_text,'MySpace'),
		'share'=>__('Post this to ', 'shrsb').'MySpace',
		'baseUrl'=>'http://www.myspace.com/Modules/PostTo/Pages/?u=PERMALINK&amp;t=TITLE',
	),
	'shr-designfloat'=>array(
		'id'=>106,
		'check'=>sprintf($checkthis_text,'DesignFloat'),
		'share'=>__('Submit this to ', 'shrsb').'DesignFloat',
		'baseUrl'=>'http://www.designfloat.com/submit.php?url=PERMALINK&amp;title=TITLE',
	),
	'shr-facebook'=>array(
		'id'=>5,
		'check'=>sprintf($checkthis_text,'Facebook'),
		'share'=>__('Share this on ', 'shrsb').'Facebook',
		'baseUrl'=>'http://www.facebook.com/share.php?v=4&amp;src=bm&amp;u=PERMALINK&amp;t=TITLE',
	),
	'shr-twitter'=>array(
		'id'=>7,
		'check'=>sprintf($checkthis_text,'Twitter'),
		'share'=>__('Tweet This!', 'shrsb'),
		'baseUrl'=>'http://twitter.com/home?status=',
	),
	'shr-mail'=>array(
		'id'=>201,
		'check'=>sprintf($checkthis_text, __("an 'Email to a Friend' link", 'shrsb')),
		'share'=>__('Email this to a friend?', 'shrsb'),
    'baseUrl'=>'mailto:?subject=%22TITLE%22&amp;body=Link: PERMALINK '.__('(sent via shareaholic)', 'shrsb').'%0D%0A%0D%0A----%0D%0A POST_SUMMARY',
	),
	'shr-tomuse'=>array(
		'id'=>294,
		'check'=>sprintf($checkthis_text,'ToMuse'),
		'share'=>__('Suggest this article to ', 'shrsb').'ToMuse',
    'baseUrl'=>'mailto:tips@tomuse.com?subject='.urlencode( __('New tip submitted via the SexyBookmarks Plugin!', 'shrsb') ).'&amp;body=Link: PERMALINK %0D%0A%0D%0A POST_SUMMARY',
	),
	'shr-comfeed'=>array(
		'check'=>sprintf($checkthis_text, __("a 'Subscribe to Comments' link", 'shrsb')),
		'share'=>__('Subscribe to the comments for this post?', 'shrsb'),
		'baseUrl'=>'PERMALINK',
	),
	'shr-linkedin'=>array(
		'id'=>88,
		'check'=>sprintf($checkthis_text,'LinkedIn'),
		'share'=>__('Share this on ', 'shrsb').'LinkedIn',
		'baseUrl'=>'http://www.linkedin.com/shareArticle?mini=true&amp;url=PERMALINK&amp;title=TITLE&amp;summary=POST_SUMMARY&amp;source=SITE_NAME',
	),
	'shr-newsvine'=>array(
		'id'=>41,
		'check'=>sprintf($checkthis_text,'Newsvine'),
		'share'=>__('Seed this on ', 'shrsb').'Newsvine',
		'baseUrl'=>'http://www.newsvine.com/_tools/seed&amp;save?u=PERMALINK&amp;h=TITLE',
	),
	'shr-googlebookmarks'=>array(
		'id'=>74,
		'check'=>sprintf($checkthis_text,'Google Bookmarks'),
		'share'=>__('Add this to ', 'shrsb').'Google Bookmarks',
		'baseUrl'=>'http://www.google.com/bookmarks/mark?op=add&amp;bkmk=PERMALINK&amp;title=TITLE',
	),
	'shr-googlereader'=>array(
		'id'=>207,
		'check'=>sprintf($checkthis_text,'Google Reader'),
		'share'=>__('Add this to ', 'shrsb').'Google Reader',
		'baseUrl'=>'http://www.google.com/reader/link?url=PERMALINK&amp;title=TITLE&amp;srcUrl=PERMALINK&amp;srcTitle=TITLE&amp;snippet=POST_SUMMARY',
	),
	'shr-googlebuzz'=>array(
		'id'=>257,
		'check'=>sprintf($checkthis_text,'Google Buzz'),
		'share'=>__('Post on Google Buzz', 'shrsb'),
		'baseUrl'=>'http://www.google.com/buzz/post?url=PERMALINK&amp;imageurl=',
	),
	'shr-misterwong'=>array(
		'id'=>6,
		'check'=>sprintf($checkthis_text,'Mister Wong'),
		'share'=>__('Add this to ', 'shrsb').'Mister Wong',
		'baseUrl'=>'http://www.mister-wong'.$wong_tld.'/addurl/?bm_url=PERMALINK&amp;bm_description=TITLE&amp;plugin=sexybookmarks',
	),
	'shr-izeby'=>array(
		'id'=>263,
		'check'=>sprintf($checkthis_text,'Izeby'),
		'share'=>__('Add this to ', 'shrsb').'Izeby',
		'baseUrl'=>'http://izeby.com/submit.php?url=PERMALINK',
	),
	'shr-tipd'=>array(
		'id'=>188,
		'check'=>sprintf($checkthis_text,'Tipd'),
		'share'=>__('Share this on ', 'shrsb').'Tipd',
		'baseUrl'=>'http://tipd.com/submit.php?url=PERMALINK',
	),
	'shr-pfbuzz'=>array(
		'id'=>279,
		'check'=>sprintf($checkthis_text,'PFBuzz'),
		'share'=>__('Share this on ', 'shrsb').'PFBuzz',
		'baseUrl'=>'http://pfbuzz.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'shr-friendfeed'=>array(
		'id'=>43,
		'check'=>sprintf($checkthis_text,'FriendFeed'),
		'share'=>__('Share this on ', 'shrsb').'FriendFeed',
		'baseUrl'=>'http://www.friendfeed.com/share?title=TITLE&amp;link=PERMALINK',
	),
	'shr-blogmarks'=>array(
		'id'=>27,
		'check'=>sprintf($checkthis_text,'BlogMarks'),
		'share'=>__('Mark this on ', 'shrsb').'BlogMarks',
		'baseUrl'=>'http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url=PERMALINK&amp;title=TITLE',
	),
	'shr-twittley'=>array(
		'id'=>277,
		'check'=>sprintf($checkthis_text,'Twittley'),
		'share'=>__('Submit this to ', 'shrsb').'Twittley',
		'baseUrl'=>'http://twittley.com/submit/?title=TITLE&amp;url=PERMALINK&amp;desc=POST_SUMMARY&amp;pcat=TWITT_CAT&amp;tags=DEFAULT_TAGS',
	),
	'shr-fwisp'=>array(
		'id'=>280,
		'check'=>sprintf($checkthis_text,'Fwisp'),
		'share'=>__('Share this on ', 'shrsb').'Fwisp',
		'baseUrl'=>'http://fwisp.com/submit?url=PERMALINK',
	),
	'shr-bobrdobr'=>array(
		'id'=>266,
		'check'=>sprintf($checkthis_text,'BobrDobr').__(' (Russian)', 'shrsb'),
		'share'=>__('Share this on ', 'shrsb').'BobrDobr',
		'baseUrl'=>'http://bobrdobr.ru/addext.html?url=PERMALINK&amp;title=TITLE',
	),
	'shr-yandex'=>array(
		'id'=>267,
		'check'=>sprintf($checkthis_text,'Yandex.Bookmarks').__(' (Russian)', 'shrsb'),
		'share'=>__('Add this to ', 'shrsb').'Yandex.Bookmarks',
		'baseUrl'=>'http://zakladki.yandex.ru/userarea/links/addfromfav.asp?bAddLink_x=1&amp;lurl=PERMALINK&amp;lname=TITLE',
	),
	'shr-memoryru'=>array(
		'id'=>269,
		'check'=>sprintf($checkthis_text,'Memory.ru').__(' (Russian)', 'shrsb'),
		'share'=>__('Add this to ', 'shrsb').'Memory.ru',
		'baseUrl'=>'http://memori.ru/link/?sm=1&amp;u_data[url]=PERMALINK&amp;u_data[name]=TITLE',
	),
	'shr-100zakladok'=>array(
		'id'=>281,
		'check'=>sprintf($checkthis_text,'100 bookmarks').__(' (Russian)', 'shrsb'),
		'share'=>__('Add this to ', 'shrsb').'100 bookmarks',
		'baseUrl'=>'http://www.100zakladok.ru/save/?bmurl=PERMALINK&amp;bmtitle=TITLE',
	),
	'shr-moemesto'=>array(
		'id'=>268,
		'check'=>sprintf($checkthis_text,'MyPlace').__(' (Russian)', 'shrsb'),
		'share'=>__('Add this to ', 'shrsb').'MyPlace',
		'baseUrl'=>'http://moemesto.ru/post.php?url=PERMALINK&amp;title=TITLE',
	),
	'shr-hackernews'=>array(
		'id'=>202,
		'check'=>sprintf($checkthis_text,'Hacker News'),
		'share'=>__('Submit this to ', 'shrsb').'Hacker News',
		'baseUrl'=>'http://news.ycombinator.com/submitlink?u=PERMALINK&amp;t=TITLE',
	),
	'shr-printfriendly'=>array(
		'id'=>236,
		'check'=>sprintf($checkthis_text,'Print Friendly'),
		'share'=>__('Send this page to ', 'shrsb').'Print Friendly',
		'baseUrl'=>'http://www.printfriendly.com/print?url=PERMALINK',
	),
	'shr-designbump'=>array(
		'id'=>282,
		'check'=>sprintf($checkthis_text,'Design Bump'),
		'share'=>__('Bump this on ', 'shrsb').'DesignBump',
		'baseUrl'=>'http://designbump.com/submit?url=PERMALINK&amp;title=TITLE&amp;body=POST_SUMMARY',
	),
	'shr-ning'=>array(
		'id'=>264,
		'check'=>sprintf($checkthis_text,'Ning'),
		'share'=>__('Add this to ', 'shrsb').'Ning',
		'baseUrl'=>'http://bookmarks.ning.com/addItem.php?url=PERMALINK&amp;T=TITLE',
	),
	'shr-identica'=>array(
		'id'=>205,
		'check'=>sprintf($checkthis_text,'Identica'),
		'share'=>__('Post this to ', 'shrsb').'Identica',
		'baseUrl'=>'http://identi.ca//index.php?action=newnotice&amp;status_textarea=Reading:+&quot;SHORT_TITLE&quot;+-+from+FETCH_URL',
	),
	'shr-xerpi'=>array(
		'id'=>20,
		'check'=>sprintf($checkthis_text,'Xerpi'),
		'share'=>__('Save this to ', 'shrsb').'Xerpi',
		'baseUrl'=>'http://www.xerpi.com/block/add_link_from_extension?url=PERMALINK&amp;title=TITLE',
	),
	'shr-techmeme'=>array(
		'id'=>204,
		'check'=>sprintf($checkthis_text,'TechMeme'),
		'share'=>__('Tip this to ', 'shrsb').'TechMeme',
		'baseUrl'=>'http://twitter.com/home/?status=Tip+@Techmeme+PERMALINK+&quot;TITLE&quot;&amp;source=shareaholic',
	),
	'shr-sphinn'=>array(
		'id'=>100,
		'check'=>sprintf($checkthis_text,'Sphinn'),
		'share'=>__('Sphinn this on ', 'shrsb').'Sphinn',
		'baseUrl'=>'http://sphinn.com/index.php?c=post&amp;m=submit&amp;link=PERMALINK',
	),
	'shr-posterous'=>array(
		'id'=>210,
		'check'=>sprintf($checkthis_text,'Posterous'),
		'share'=>__('Post this to ', 'shrsb').'Posterous',
		'baseUrl'=>'http://posterous.com/share?linkto=PERMALINK&amp;title=TITLE&amp;selection=POST_SUMMARY',
	),
	'shr-globalgrind'=>array(
		'id'=>89,
		'check'=>sprintf($checkthis_text,'Global Grind'),
		'share'=>__('Grind this! on ', 'shrsb').'Global Grind',
		'baseUrl'=>'http://globalgrind.com/submission/submit.aspx?url=PERMALINK&amp;type=Article&amp;title=TITLE',
	),
	'shr-pingfm'=>array(
		'id'=>45,
		'check'=>sprintf($checkthis_text,'Ping.fm'),
		'share'=>__('Ping this on ', 'shrsb').'Ping.fm',
		'baseUrl'=>'http://ping.fm/ref/?link=PERMALINK&amp;title=TITLE&amp;body=POST_SUMMARY',
	),
	'shr-nujij'=>array(
		'id'=>238,
		'check'=>sprintf($checkthis_text,'NUjij').__(' (Dutch)', 'shrsb'),
		'share'=>__('Submit this to ', 'shrsb').'NUjij',
		'baseUrl'=>'http://nujij.nl/jij.lynkx?t=TITLE&amp;u=PERMALINK&amp;b=POST_SUMMARY',
	),
	'shr-ekudos'=>array(
		'id'=>283,
		'check'=>sprintf($checkthis_text,'eKudos').__(' (Dutch)', 'shrsb'),
		'share'=>__('Submit this to ', 'shrsb').'eKudos',
		'baseUrl'=>'http://www.ekudos.nl/artikel/nieuw?url=PERMALINK&amp;title=TITLE&amp;desc=POST_SUMMARY',
	),
	'shr-netvouz'=>array(
		'id'=>21,
		'check'=>sprintf($checkthis_text,'Netvouz'),
		'share'=>__('Submit this to ', 'shrsb').'Netvouz',
		'baseUrl'=>'http://www.netvouz.com/action/submitBookmark?url=PERMALINK&amp;title=TITLE&amp;popup=no',
	),
	'shr-netvibes'=>array(
		'id'=>195,
		'check'=>sprintf($checkthis_text,'Netvibes'),
		'share'=>__('Submit this to ', 'shrsb').'Netvibes',
		'baseUrl'=>'http://www.netvibes.com/share?title=TITLE&amp;url=PERMALINK',
	),
	'shr-webblend'=>array(
		'id'=>284,
		'check'=>sprintf($checkthis_text,'Web Blend'),
		'share'=>__('Blend this!', 'shrsb'),
		'baseUrl'=>'http://thewebblend.com/submit?url=PERMALINK&amp;title=TITLE&amp;body=POST_SUMMARY',
	),
	'shr-wykop'=>array(
		'id'=>285,
		'check'=>sprintf($checkthis_text,'Wykop').__(' (Polish)', 'shrsb'),
		'share'=>__('Add this to Wykop!', 'shrsb'),
		'baseUrl'=>'http://www.wykop.pl/dodaj?url=PERMALINK&amp;title=TITLE',
	),
	'shr-blogengage'=>array(
		'id'=>286,
		'check'=>sprintf($checkthis_text,'BlogEngage'),
		'share'=>__('Engage with this article!', 'shrsb'),
		'baseUrl'=>'http://www.blogengage.com/submit.php?url=PERMALINK',
	),
	'shr-hyves'=>array(
		'id'=>105,
		'check'=>sprintf($checkthis_text,'Hyves'),
		'share'=>__('Share this on ', 'shrsb').'Hyves',
		'baseUrl'=>'http://www.hyves.nl/profilemanage/add/tips/?name=TITLE&amp;text=POST_SUMMARY+-+PERMALINK&amp;rating=5',
	),
	'shr-pusha'=>array(
		'id'=>59,
		'check'=>sprintf($checkthis_text,'Pusha').__(' (Swedish)', 'shrsb'),
		'share'=>__('Push this on ', 'shrsb').'Pusha',
		'baseUrl'=>'http://www.pusha.se/posta?url=PERMALINK&amp;title=TITLE',
	),
	'shr-hatena'=>array(
		'id'=>246,
		'check'=>sprintf($checkthis_text,'Hatena Bookmarks').__(' (Japanese)', 'shrsb'),
		'share'=>__('Bookmarks this on ', 'shrsb').'Hatena Bookmarks',
		'baseUrl'=>'http://b.hatena.ne.jp/add?mode=confirm&amp;url=PERMALINK&amp;title=TITLE',
	),
	'shr-mylinkvault'=>array(
		'id'=>98,
		'check'=>sprintf($checkthis_text,'MyLinkVault'),
		'share'=>__('Store this link on ', 'shrsb').'MyLinkVault',
		'baseUrl'=>'http://www.mylinkvault.com/link-page.php?u=PERMALINK&amp;n=TITLE',
	),
	'shr-slashdot'=>array(
		'id'=>61,
		'check'=>sprintf($checkthis_text,'SlashDot'),
		'share'=>__('Submit this to ', 'shrsb').'SlashDot',
		'baseUrl'=>'http://slashdot.org/bookmark.pl?url=PERMALINK&amp;title=TITLE',
	),
	'shr-squidoo'=>array(
		'id'=>46,
		'check'=>sprintf($checkthis_text,'Squidoo'),
		'share'=>__('Add to a lense on ', 'shrsb').'Squidoo',
		'baseUrl'=>'http://www.squidoo.com/lensmaster/bookmark?PERMALINK',
	),
	'shr-propeller'=>array(
		'id'=>77,
		'check'=>sprintf($checkthis_text,'Propeller'),
		'share'=>__('Submit this story to ', 'shrsb').'Propeller',
		'baseUrl'=>'http://www.propeller.com/submit/?url=PERMALINK',
	),
	'shr-faqpal'=>array(
		'id'=>287,
		'check'=>sprintf($checkthis_text,'FAQpal'),
		'share'=>__('Submit this to ', 'shrsb').'FAQpal',
		'baseUrl'=>'http://www.faqpal.com/submit?url=PERMALINK',
	),
	'shr-evernote'=>array(
		'id'=>191,
		'check'=>sprintf($checkthis_text,'Evernote'),
		'share'=>__('Clip this to ', 'shrsb').'Evernote',
		'baseUrl'=>'http://www.evernote.com/clip.action?url=PERMALINK&amp;title=TITLE',
	),
	'shr-meneame'=>array(
		'id'=>33,
		'check'=>sprintf($checkthis_text,'Meneame').__(' (Spanish)', 'shrsb'),
		'share'=>__('Submit this to ', 'shrsb').'Meneame',
		'baseUrl'=>'http://meneame.net/submit.php?url=PERMALINK',
	),
	'shr-bitacoras'=>array(
		'id'=>288,
		'check'=>sprintf($checkthis_text,'Bitacoras').__(' (Spanish)', 'shrsb'),
		'share'=>__('Submit this to ', 'shrsb').'Bitacoras',
		'baseUrl'=>'http://bitacoras.com/anotaciones/PERMALINK',
	),
	'shr-jumptags'=>array(
		'id'=>14,
		'check'=>sprintf($checkthis_text,'JumpTags'),
		'share'=>__('Submit this link to ', 'shrsb').'JumpTags',
		'baseUrl'=>'http://www.jumptags.com/add/?url=PERMALINK&amp;title=TITLE',
	),
	'shr-bebo'=>array(
		'id'=>196,
		'check'=>sprintf($checkthis_text,'Bebo'),
		'share'=>__('Share this on ', 'shrsb').'Bebo',
		'baseUrl'=>'http://www.bebo.com/c/share?Url=PERMALINK&amp;Title=TITLE',
	),
	'shr-n4g'=>array(
		'id'=>289,
		'check'=>sprintf($checkthis_text,'N4G'),
		'share'=>__('Submit tip to ', 'shrsb').'N4G',
		'baseUrl'=>'http://www.n4g.com/tips.aspx?url=PERMALINK&amp;title=TITLE',
	),
	'shr-strands'=>array(
		'id'=>190,
		'check'=>sprintf($checkthis_text,'Strands'),
		'share'=>__('Submit this to ', 'shrsb').'Strands',
		'baseUrl'=>'http://www.strands.com/tools/share/webpage?title=TITLE&amp;url=PERMALINK',
	),
	'shr-orkut'=>array(
		'id'=>247,
		'check'=>sprintf($checkthis_text,'Orkut'),
		'share'=>__('Promote this on ', 'shrsb').'Orkut',
		'baseUrl'=>'http://promote.orkut.com/preview?nt=orkut.com&amp;tt=TITLE&amp;du=PERMALINK&amp;cn=POST_SUMMARY',
	),
	'shr-tumblr'=>array(
		'id'=>78,
		'check'=>sprintf($checkthis_text,'Tumblr'),
		'share'=>__('Share this on ', 'shrsb').'Tumblr',
		'baseUrl'=>'http://www.tumblr.com/share?v=3&amp;u=PERMALINK&amp;t=TITLE',
	),
	'shr-stumpedia'=>array(
		'id'=>192,
		'check'=>sprintf($checkthis_text,'Stumpedia'),
		'share'=>__('Add this to ', 'shrsb').'Stumpedia',
		'baseUrl'=>'http://www.stumpedia.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'shr-current'=>array(
		'id'=>80,
		'check'=>sprintf($checkthis_text,'Current'),
		'share'=>__('Post this to ', 'shrsb').'Current',
		'baseUrl'=>'http://current.com/clipper.htm?url=PERMALINK&amp;title=TITLE',
	),
	'shr-blogger'=>array(
		'id'=>219,
		'check'=>sprintf($checkthis_text,'Blogger'),
		'share'=>__('Blog this on ', 'shrsb').'Blogger',
		'baseUrl'=>'http://www.blogger.com/blog_this.pyra?t&amp;u=PERMALINK&amp;n=TITLE&amp;pli=1',
	),
	'shr-plurk'=>array(
		'id'=>218,
		'check'=>sprintf($checkthis_text,'Plurk'),
		'share'=>__('Share this on ', 'shrsb').'Plurk',
		'baseUrl'=>'http://www.plurk.com/m?content=TITLE+-+PERMALINK&amp;qualifier=shares',
	),
	'shr-dzone'=>array(
		'id'=>102,
		'check'=>sprintf($checkthis_text,'DZone'),
		'share'=>__('Add this to ', 'shrsb').'DZone',
		'baseUrl'=>'http://www.dzone.com/links/add.html?url=PERMALINK&amp;title=TITLE&amp;description=POST_SUMMARY',
	),
	'shr-kaevur'=>array(
		'id'=>290,
		'check'=>sprintf($checkthis_text,'Kaevur').__(' (Estonian)', 'shrsb'),
		'share'=>__('Share this on ', 'shrsb').'Kaevur',
		'baseUrl'=>'http://kaevur.com/submit.php?url=PERMALINK',
	),
	'shr-virb'=>array(
		'id'=>291,
		'check'=>sprintf($checkthis_text,'Virb'),
		'share'=>__('Share this on ', 'shrsb').'Virb',
		'baseUrl'=>'http://virb.com/share?external&amp;v=2&amp;url=PERMALINK&amp;title=TITLE',
	),
	'shr-box'=>array(
		'id'=>240,
		'check'=>sprintf($checkthis_text,'Box.net'),
		'share'=>__('Add this link to ', 'shrsb').'Box.net',
		'baseUrl'=>'https://www.box.net/api/1.0/import?url=PERMALINK&amp;name=TITLE&amp;description=POST_SUMMARY&amp;import_as=link',
	),
	'shr-oknotizie'=>array(
		'id'=>243,
		'check'=>sprintf($checkthis_text,'OkNotizie').__('(Italian)', 'shrsb'),
		'share'=>__('Share this on ', 'shrsb').'OkNotizie',
		'baseUrl'=>'http://oknotizie.virgilio.it/post?url=PERMALINK&amp;title=TITLE',
	),
	'shr-bonzobox'=>array(
		'id'=>292,
		'check'=>sprintf($checkthis_text,'BonzoBox'),
		'share'=>__('Add this to ', 'shrsb').'BonzoBox',
		'baseUrl'=>'http://bonzobox.com/toolbar/add?pop=1&amp;u=PERMALINK&amp;t=TITLE&amp;d=POST_SUMMARY',
	),
	'shr-plaxo'=>array(
		'id'=>44,
		'check'=>sprintf($checkthis_text,'Plaxo'),
		'share'=>__('Share this on ', 'shrsb').'Plaxo',
		'baseUrl'=>'http://www.plaxo.com/?share_link=PERMALINK',
	),
	'shr-springpad'=>array(
		'id'=>265,
		'check'=>sprintf($checkthis_text,'SpringPad'),
		'share'=>__('Spring this on ', 'shrsb').'SpringPad',
		'baseUrl'=>'http://springpadit.com/clip.action?body=POST_SUMMARY&amp;url=PERMALINK&amp;format=microclip&amp;title=TITLE&amp;isSelected=true',
	),
	'shr-zabox'=>array(
		'id'=>293,
		'check'=>sprintf($checkthis_text,'Zabox'),
		'share'=>__('Box this on ', 'shrsb').'Zabox',
		'baseUrl'=>'http://www.zabox.net/submit.php?url=PERMALINK',
	),
	'shr-viadeo'=>array(
		'id'=>92,
		'check'=>sprintf($checkthis_text,'Viadeo'),
		'share'=>__('Share this on ', 'shrsb').'Viadeo',
		'baseUrl'=>'http://www.viadeo.com/shareit/share/?url=PERMALINK&amp;title=TITLE&amp;urlaffiliate=31138',
	),
	'shr-gmail'=>array(
		'id'=>52,
		'check'=>sprintf($checkthis_text,'Gmail'),
		'share'=>__('Email this via ', 'shrsb').'Gmail',
		'baseUrl'=>'https://mail.google.com/mail/?ui=2&amp;view=cm&amp;fs=1&amp;tf=1&amp;su=TITLE&amp;body=Link: PERMALINK '.__('(sent via shareaholic)', 'shrsb').'%0D%0A%0D%0A----%0D%0A POST_SUMMARY',
	),
	'shr-hotmail'=>array(
		'id'=>53,
		'check'=>sprintf($checkthis_text,'Hotmail'),
		'share'=>__('Email this via ', 'shrsb').'Hotmail',
		'baseUrl'=>'http://mail.live.com/?rru=compose?subject=TITLE&amp;body=Link: PERMALINK '.__('(sent via shareaholic)', 'shrsb').'%0D%0A%0D%0A----%0D%0A POST_SUMMARY',
	),
	'shr-yahoomail'=>array(
		'id'=>54,
		'check'=>sprintf($checkthis_text,'Yahoo! Mail'),
		'share'=>__('Email this via ', 'shrsb').'Yahoo! Mail',
		'baseUrl'=>'http://compose.mail.yahoo.com/?Subject=TITLE&amp;body=Link: PERMALINK '.__('(sent via shareaholic)', 'shrsb').'%0D%0A%0D%0A----%0D%0A POST_SUMMARY',
	),
	'shr-bzzster'=>array(
		'id'=>1,
		'check'=>sprintf($checkthis_text,'Buzzster!'),
		'share'=>__('Share this via ', 'shrsb').'Buzzster!',
		'baseUrl'=>"javascript:var%20s=document.createElement('script');s.src='http://www.buzzster.com/javascripts/bzz_adv.js';s.type='text/javascript';void(document.getElementsByTagName('head')[0].appendChild(s));",
	),
);
ksort($shrsb_bookmarks_data, SORT_STRING); //sort array by keys
?>
