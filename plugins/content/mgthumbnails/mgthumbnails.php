<?php
/**
* @copyright Copyright (C) 2012 Echo WebDesign. All rights reserved.
* @license GNU/GPL
*
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.image.image' );

/**
* mgThumbnail Content Plugin
*
*/
class plgContentMgthumbnails extends JPlugin
{

	/**
	* Constructor
	*
	* @param object $subject The object to observe
	* @param object $params The object that holds the plugin parameters
	*/
	function plgContentMgthumbnails( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	* Example prepare content method
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	* @param int The 'page' number
	*/
	function onContentPrepare( $context, &$article, &$params, $page = 0 )
	{
		global $mainframe;

	  $basepage = JURI::base();		
	  
	  $view = JRequest::getVar('view','article','REQUEST');
		$blog_frontpage = $this->params->get('blog_frontpage','1');
		if ($blog_frontpage == '0' && ($view == 'category' || $view == 'featured' || $view == 'categories')) {
			return true;
		}
		
		$fulltext = $this->params->get('fulltext','1');
	  if ($fulltext == '0' && $view == 'article') {
			return true;
		}
	  	  
		$document = JFactory::getDocument();
    $uniq_group_id = uniqid();
		preg_match_all('/<img[^>]+>/i',$article->text, $imageArr);
		
		if (count(array_filter($imageArr[0])) > 0) {
			foreach ($imageArr[0] as $image) {
				$replace = $this->doImageChange($image, $article, $uniq_group_id);
				$article->text = str_replace($image, $replace, $article->text);
			}
		} else { return true; }


		$swScript = $this->params->get('swScript','fancybox');
		switch ($swScript) {
		//BEGIN fancybox script
			case 'fancybox':
				$overlaycolor = $this->params->get('jqoverlaycolor','000000');
				$overlayopacity = $this->params->get('jqoverlayopacity', 0.9);
				$width = $this->params->get('jqwindowwidth', 0);
				$height = $this->params->get('jqwindowheight', 0);
//				if ($width != 0) {$width = '\'width\': \''.$width.'px\',';} else {$width = '';}
//				if ($height != 0) {$height = '\'height\': \''.$height.'px\',';} else {$height = '';}
				$title = $this->params->get('jqshowtitle', true);
				$loop = $this->params->get('jqgalleryloop', true);
				$animtype = $this->params->get('jqanimation', 'fade');
				$padding = $this->params->get('jqwindowpadding', 0);

		    $fancybox = '    
					$(document).ready(function() {
			      $("a.mgthumbnails[rel=group_'.$uniq_group_id.']").fancybox({
																										        			\'padding\'				: '.$padding.',
																										        			\'overlayOpacity\': '.$overlayopacity.',
																										        			\'overlayColor\'	: \'#'.$overlaycolor.'\',
																										        			\'autoScale\'			: true,
																										        			\'transitionIn\'	: \''.$animtype.'\',
																										        			\'transitionOut\'	: \''.$animtype.'\',
																										        			\'showNavArrows\'	: true,
																										        			\'titleShow\'			: '.$title.',
																										        			\'type\'					: \'image\',
																										        			\'cyclic\'				: '.$loop.'
																										        		});
		    	});';
		
			    $document->addScriptDeclaration($fancybox);
			break;
		//END fancybox script

		//BEGIN shadowbox script	
			case 'shadowbox':
				$overlaycolor = $this->params->get('sboverlaycolor', '000000');
				$overlayopacity = $this->params->get('sboverlayopacity', 0.8);
				$loop = $this->params->get('sbgalleryloop', true);
				$animation = $this->params->get('sbanimation', true);
				$fade = $this->params->get('sbanimatefade', true);
				$resizespeed = $this->params->get('sbresizespeed', 0.25);
				$fadespeed = $this->params->get('sbfadespeed', 0.25);
				$slideshowtime = $this->params->get('sbslideshowtime', 0);
				$padding = $this->params->get('sbpadding', 20);
				$sbnav = $this->params->get('sbnav', true);
				$sbcounter = $this->params->get('sbcounter', true);
			
			$shadowboxCode = '
				window.onload = function() {
			    Shadowbox.setup("a.mgthumbnails", {
							gallery:	"group_'.$uniq_group_id.'",
							overlayColor: "#'.$overlaycolor.'",
							overlayOpacity: '.$overlayopacity.',
							continuous: '.$loop.',
							animate: '.$animation.',
							animateFade: '.$fade.',
							resizeDuration: '.$resizespeed.',
							slideshowDelay: '.$slideshowtime.',
							viewportPadding: '.$padding.',
							fadeDuration: '.$fadespeed.',
							displayNav: '.$sbnav.',
							displayCounter: '.$sbcounter.',
							handleOversize: "resize"
			    });
				};
			';	
					
			$document->addScriptDeclaration($shadowboxCode);
			break;
		//END shadowbox script

		//BEGIN jquery slimbox2 script	
			case 'slimbox-jq':
				$loop = $this->params->get('slim2galleryloop', true);
				$overlayopacity = $this->params->get('slim2overlayopacity', 0.90);
				$overlayfadespeed = $this->params->get('slim2overlayfadespeed', 300);
				$imagefadespeed = $this->params->get('slim2imagefadespeed', 300);
				$resizespeed = $this->params->get('slim2resizespeed', 300);
				$captionspeed = $this->params->get('slim2captionspeed', 300);
				$countertext = $this->params->get('slim2countertext', 'Image {x} of {y}');

			$slimboxCode = '
				jqslimbox = jQuery.noConflict();
				jqslimbox(document).ready(function() {
					jqslimbox("a.mgthumbnails_'.$uniq_group_id.'").slimbox(
						{
							loop: '.$loop.',
							overlayOpacity: '.$overlayopacity.',
							overlayFadeDuration: '.$overlayfadespeed.',
							imageFadeDuration: '.$imagefadespeed.',
							resizeDuration: '.$resizespeed.',
							captionAnimationDuration: '.$captionspeed.',
							counterText: "'.$countertext.'"
						}
					);
				});
			';
			$document->addScriptDeclaration($slimboxCode);
			break;
		//END jquery slimbox2 script	
			
		//BEGIN mootools slimbox script	
			case 'slimbox-mt':
				$loop = $this->params->get('slimgalleryloop', true);
				$overlayopacity = $this->params->get('slimoverlayopacity', 0.90);
				$overlayfadespeed = $this->params->get('slimoverlayfadespeed', 300);
				$imagefadespeed = $this->params->get('slimimagefadespeed', 300);
				$resizespeed = $this->params->get('slimresizespeed', 300);
				$captionspeed = $this->params->get('slimcaptionspeed', 300);
				$countertext = $this->params->get('slimcountertext', 'Image {x} of {y}');

			$slimboxCode = '
				window.addEvent("domready", function() {
					$$("a.mgthumbnails_'.$uniq_group_id.'").slimbox(
						{
							loop: '.$loop.',
							overlayOpacity: '.$overlayopacity.',
							overlayFadeDuration: '.$overlayfadespeed.',
							imageFadeDuration: '.$imagefadespeed.',
							resizeDuration: '.$resizespeed.',
							captionAnimationDuration: '.$captionspeed.',
							counterText: "'.$countertext.'"
						}
					);
				});
			';
			$document->addScriptDeclaration($slimboxCode);
			break;
		//END mootools slimbox script	
			
		//BEGIN greybox script	
			case 'greybox':
			//no options found on home site... suck
			break;
		//END greybox script	
			
		//BEGIN colorbox script	
			case 'colorbox':
				$width = $this->params->get('cbwindowwidth',0);
				$height = $this->params->get('cbwindowheight',0);
		    if ($width != 0) {$width = 'maxWidth: '.$width.',';} else {$width = 'maxWidth: "95%",';}
		    if ($height != 0) {$height = 'maxHeight: '.$height.',';} else {$height = 'maxHeight: "95%",';}
				$showTitle = $this->params->get('cbshowtitle', true);
				$slideshow = $this->params->get('cbslideshow', true);
				$effect = $this->params->get('cbanimationtype', 'fade');
				$slideshowspeed = $this->params->get('cbslideshowspeed', 4000);
				$opacity = $this->params->get('cboverlayopacity', 0.8);
			  $animspeed = $this->params->get('cbanimationspeed', 350);
			  $loop = $this->params->get('cbgalleryloop', false); 
				$colorbox = ' 
					var mgThumbnails = jQuery.noConflict();     
						mgThumbnails(document).ready(function() {
			      mgThumbnails("a.mgthumbnails").colorbox({
																										'.$width.'
																										'.$height.'
																										"transition"		: "'.$effect.'",
																										"slideshow"			: '.$slideshow.',
																										"slideshowSpeed": '.$slideshowspeed.',
																										"slideshowAuto" : false,
																										"speed"					: '.$animspeed.',
																										"title"         : '.$showTitle.',
																										"opacity"       : '.$opacity.',
																										"loop"   				: '.$loop.',
																				        		});
		    	});';
		    	
			    $document->addScriptDeclaration($colorbox);
			break;
		//END colorbox script	
			
		//BEGIN joomla modal script	
			case 'modal':
			default:
		//END joomla modal script	
		}
		return true;
	} //end onContentPrepare function
		
	
	function onBeforeCompileHead()
	{ 	  
		$basepage = JURI::base();		
		$document = JFactory::getDocument();

	//get plugin params
		$swScript = $this->params->get('swScript','fancybox');
		switch ($swScript) {
			case 'fancybox':
				$jqlibrary = $this->params->get('jqlib',1);
				$jqfancylibrary = $this->params->get('jqfancylib',1);

		    if ($jqlibrary == 1) $document->addScript( 'plugins/content/mgthumbnails/fancybox/jquery-1.7.1.min.js' );
		    if ($jqfancylibrary == 1) {								
						$data = $document->getHeadData();
					//test if fancybox css exists in header
						$controlCSS = '';
						foreach ($data['styleSheets'] as $key=>$value)
						{
							$controlCSS += preg_match('(fancybox([0-9\-\.]+).css)',$key);
						}
					//controlCSS equals 0 so fancybox css was not found, we can add ours
						if ($controlCSS == 0) {$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/fancybox/jquery.fancybox-1.3.4.css' );}
					//adding the fancybox script. the file contains the verifycation about existence of fancybox.js
						$document->addScript( $basepage.'plugins/content/mgthumbnails/fancybox/jquery.fancybox-1.3.4.pack.js' );
				}			
			break;
			
			case 'shadowbox':
				$shadowboxlib = $this->params->get('shadowboxlib',1);
		    if ($shadowboxlib == 1) $document->addScript( 'plugins/content/mgthumbnails/shadowbox/shadowbox.js' );
				$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/shadowbox/shadowbox.css' );
				$shadowboxCode = '
					Shadowbox.init();
				';
				$document->addScriptDeclaration($shadowboxCode);
			break;
			
			case 'slimbox-jq':
				$jqlib = $this->params->get('jqlib',1);
				$slimboxjq = $this->params->get('slimboxjq',1);
		    if ($jqlib == 1) $document->addScript( 'plugins/content/mgthumbnails/fancybox/jquery-1.7.1.min.js' );
		    if ($slimboxjq == 1) $document->addScript( 'plugins/content/mgthumbnails/slimbox2-jq/slimbox2.js' );
				$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/slimbox2-jq/slimbox2.css' );
			break;
			
			case 'slimbox-mt':
				$mootools = $this->params->get('mtlib',1);
				$slimboxmt = $this->params->get('slimboxmt',1);
		    if ($mootools == 1) $document->addScript( 'plugins/content/mgthumbnails/slimbox18-mt13/mootools.js' );
		    if ($slimboxmt == 1) $document->addScript( 'plugins/content/mgthumbnails/slimbox18-mt13/slimbox.js' );
				$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/slimbox18-mt13/slimbox.css' );
			break;
			
			case 'greybox':
				$greylib = $this->params->get('greyboxlib',1);
				if ($greylib == 1) {
					$document->addScript( 'plugins/content/mgthumbnails/greybox/getroot.js' );
					$document->addScript( 'plugins/content/mgthumbnails/greybox/AJS.js' );
					$document->addScript( 'plugins/content/mgthumbnails/greybox/AJS_fx.js' );
					$document->addScript( 'plugins/content/mgthumbnails/greybox/gb_scripts.js' );
				}
				$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/greybox/gb_styles.css' );
			break;
			
			case 'colorbox':
				$jqlib = $this->params->get('jqlib',1);
				$colorbox = $this->params->get('colorboxlib',1);
				$document->addScript( 'plugins/content/mgthumbnails/greybox/getroot.js' );
		    if ($jqlib == 1) $document->addScript( 'plugins/content/mgthumbnails/fancybox/jquery-1.7.1.min.js' );
		    if ($colorbox == 1) $document->addScript( 'plugins/content/mgthumbnails/colorbox/jquery.colorbox.js' );
				$document->addStylesheet( $basepage.'plugins/content/mgthumbnails/colorbox/colorbox-slideshow.css' );
			break;
			
			case 'modal':
			default:
				JHTML::_('behavior.modal');
		}
		return;  
	} //end onBeforeCompileHead function 
	
	
	function doImageChange( $imgTag, &$article, $uniq_group_id )
	{	
		preg_match_all('/(width|height|src|title|class)=("[^"]*")/i', $imgTag, $imgArr);
		
		$countAtr = count($imgArr[0]);
		$img = array();
		for ($i=0;$i<$countAtr;$i++) {
			$img[$imgArr[1][$i]] = str_replace('"','',$imgArr[2][$i]);
		}

	//$activation = $this->params->get('activation','resized');
		//check if image is crossdomained. If yes cancel action.
		$http = substr_count($img['src'], 'http://');
		$https = substr_count($img['src'], 'https://');
		$base = substr_count($img['src'], JURI::base());
		if (($http > 0 || $https > 0) && $base == 0) {
			return $imgTag;  //cross domained image return original image tag
		}
				
		if (!$img['width'] && !$img['height']) {
			return $imgTag;  //not resized image return original image tag
		}

	//excluded classes will be ignored
		if ($img['class'] != '' || $img['class'] != '') {
			$excClasses = $this->params->get('exclude_classes');
			$excClassArr = explode(',',$excClasses);
			$classArr = explode (" ",$img['class']);
			$controlArr = array_intersect($excClassArr, $classArr);
			if (count($controlArr) > 0) return $imgTag; //ignored class return original image tag
		}		

	//load image
		$mgImage = new JImage;
		$mgImage->loadFile(urldecode(JPATH_ROOT.'/'.$img['src']));

		if (!$img['width']) {
		//if width not given
			$ratio = $mgImage->getWidth() / $mgImage->getHeight();
		  $img['width'] = round($img['height'] * $ratio, 0);
		} elseif (!$img['height']) {
		//if height not given
			$ratio = $mgImage->getHeight() / $mgImage->getWidth();
		  $img['height'] = round($img['width'] * $ratio, 0);
		} 
	
			//create thumbanil filename
				$thumbPath = $this->params->get('mgThumbPath');
				//if directory not exists, create it
				jimport( 'joomla.filesystem.folder' );
				JFolder::create($thumbPath);
				$imgThumbName = $img['width'].'x'.$img['height'].'-'.str_replace('/','-',$img['src']);
		
			//if thumbnail image already created we can skip creating thumbnail
				if (!file_exists(JPATH_ROOT.'/'.$thumbPath.$imgThumbName)) {		
				//lets create thumbanil
				//resize image
					$mgImage->resize($img['width'], $img['height'], false);
					
				//write out resized image file
					$thumbQuality = $this->params->get('thumbQuality');
					$mgImage->toFile(JPATH_ROOT.'/'.$thumbPath.$imgThumbName, $type, array('quality' => $thumbQuality ));
				}
		
	//replace src with the new thumbnail image
		$reImgTag = preg_replace('|(src\=\"([0-9a-zA-Z\-\_\.\/]+)\")|i', ' src="'.JURI::base().$thumbPath.$imgThumbName.'" ', $imgTag );

	//create link title attribute
		if ($img['title']!='' || $img['title'])
			$title = ' title="'.$img['title'].'" ';
    else
      $title = '';
		
	//create new html code for image and link
		$buffer = '';
		$swScript = $this->params->get('swScript','fancybox');
		switch ($swScript) {
			case 'fancybox':
			$buffer .= '
				<a class="mgthumbnails" href="'.JURI::base().$img['src'].'" '.$title.' rel="group_'.$uniq_group_id.'">'.$reImgTag.'</a>
			';
			break;
			
			case 'shadowbox':
			$buffer .= '
				<a class="mgthumbnails" href="'.JURI::base().$img['src'].'" '.$title.' rel="Shadowbox[group_'.$uniq_group_id.']">'.$reImgTag.'</a>
			';
			break;
			
			case 'slimbox-jq':
			$buffer .= '
				<a class="mgthumbnails_'.$uniq_group_id.'" href="'.JURI::base().$img['src'].'" '.$title.' rel="lightbox">'.$reImgTag.'</a>
			';
			break;
			
			case 'slimbox-mt':
			$buffer .= '
				<a class="mgthumbnails_'.$uniq_group_id.'" href="'.JURI::base().$img['src'].'" '.$title.' rel="lightbox">'.$reImgTag.'</a>
			';
			break;
			
			case 'greybox':
			$buffer .= '
				<a class="mgthumbnails" href="'.JURI::base().$img['src'].'" '.$title.' rel="gb_imageset[group_'.$uniq_group_id.']">'.$reImgTag.'</a>
			';
			break;
			                                                                             				
			case 'colorbox':
			$buffer .= '
				<a class="mgthumbnails" href="'.JURI::base().$img['src'].'" '.$title.' rel="group_'.$uniq_group_id.'">'.$reImgTag.'</a>
			';
			break;
			
			case 'modal':
			default:
			$buffer .= '
				<a class="modal" href="'.JURI::base().$img['src'].'" '.$title.' >'.$reImgTag.'</a>
			';
		}
    return $buffer; 
  } //end doImageChange function

} //end class
