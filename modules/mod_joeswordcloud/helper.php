<?php
/**
 * Helper class for Joes Word Cloud module
 *
 * @package    Joes Joomla
 * @subpackage Modules
 * @link www.joellipman.com
 * @license        GNU GPL v3
 * Displays a cluster of the words from your Joomla! articles (core content not meta data... less manual setup).
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJoesWordCloudHelper
{

	/**
     * Gets all the words from the content database (title + intro)
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
    function getModuleContent( $params )
    {

		/**
	     * Retrieves a string and returns a string of all the keywords separated by a space.
	     *
	     * @param string $str
		 * @returns string $str
	     * @access public
	     */
	 if (!function_exists('joes_return_keywords')) {
		function joes_return_keywords($str) {
		    $urlbrackets    = '\[\]\(\)';
		    $urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
		    $urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
		    $urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;
		    $specialquotes  = '\'"\*<>';
		    $fullstop       = '\x{002E}\x{FE52}\x{FF0E}';
		    $comma          = '\x{002C}\x{FE50}\x{FF0C}';
		    $arabsep        = '\x{066B}\x{066C}';
		    $numseparators  = $fullstop . $comma . $arabsep;
		    $numbersign     = '\x{0023}\x{FE5F}\x{FF03}';
		    $percent        = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
		    $prime          = '\x{2032}\x{2033}\x{2034}\x{2057}';
		    $nummodifiers   = $numbersign . $percent . $prime;

			$plus   = '\+\x{FE62}\x{FF0B}\x{208A}\x{207A}';
		    $minus  = '\x{2012}\x{208B}\x{207B}';
		    $units  = '\\x{00B0}\x{2103}\x{2109}\\x{23CD}';
		    $units .= '\\x{32CC}-\\x{32CE}';
		    $units .= '\\x{3300}-\\x{3357}';
		    $units .= '\\x{3371}-\\x{33DF}';
		    $units .= '\\x{33FF}';
		    $ideo   = '\\x{2E80}-\\x{2EF3}';
		    $ideo  .= '\\x{2F00}-\\x{2FD5}';
		    $ideo  .= '\\x{2FF0}-\\x{2FFB}';
		    $ideo  .= '\\x{3037}-\\x{303F}';
		    $ideo  .= '\\x{3190}-\\x{319F}';
		    $ideo  .= '\\x{31C0}-\\x{31CF}';
		    $ideo  .= '\\x{32C0}-\\x{32CB}';
		    $ideo  .= '\\x{3358}-\\x{3370}';
		    $ideo  .= '\\x{33E0}-\\x{33FE}';
		    $ideo  .= '\\x{A490}-\\x{A4C6}';

			$str = html_entity_decode( $str, ENT_QUOTES, "UTF-8" );	// decode html entities into UTF-8 (symbols)
			$str = strip_tags($str);
			$search = array (

				# strip known tags
				'@<script[^>]*?>.*?</script>@si',		// Strip out javascript
				'@<style[^>]*?>.*?</style>@si',			// Strip out Inline Stylesheets
				'@[{].*?[}]@si',						// Strip out Internal Joomla Curly Brackets commands?
		        '@<[\/\!]*?[^<>]*?>@si',				// Strip out HTML tags
		        '/&.{0,}?;/',							// Strip out Numerical entities
		        '@([\r\n])[\s]+@',						// Strip out white space
				'/<!--\[if[^\]]*]>.*?<!\[endif\]-->/i', // Strip out MSOffice code

				# strip punctuation
	            '/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
	            '/\p{Po}(?<![' . $specialquotes . $numseparators . $urlall . $nummodifiers . '])/u',
	            '/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
	            '/[' . $specialquotes . $numseparators . $urlspaceafter .
                '\p{Pd}\p{Pc}]+((?= )|$)/u',
	            '/((?<= )|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
	            '/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',

				# strip symbols
	            '/[\p{Sk}\p{Co}]/u',
				'/\p{Sm}(?<![' . $plus . $minus . '=~\x{2044}])/u',
				'/((?<= )|^)[' . $plus . $minus . ']+((?![\p{N}\p{Sc}])|$)/u',
				'/((?<= )|^)=+/u',
				'/[' . $plus . $minus . '=~]+((?= )|$)/u',
				'/\p{So}(?<![' . $units . $ideo . '])/u',

				# remove other empty characters
				'/ +/'			// removes double spaces
			);
			$str = preg_replace($search, ' ', $str);

			return $str;
		}
	 }

		# counts string length taking into account international characters
	 if (!function_exists('joes_utf8_strlen')) {
		function joes_utf8_strlen($str) {
		    $count = 0;
		    for($i = 0; $i < strlen($str); $i++) {
		        $value = ord($str[$i]);
		        if($value > 127) {
		            if($value >= 192 && $value <= 223)
		                $i++;
		            elseif($value >= 224 && $value <= 239)
		                $i = $i + 2;
		            elseif($value >= 240 && $value <= 247)
		                $i = $i + 3;
				}
			    $count++;
			}
			return $count;
		}
	 }

		# declare
		global $mainframe;

		$modulecontent=$sql_query=$sql_accessconditions='';
		$count=$countvalidrows=$countvalidwords=$total_word_count=0;
		$freqData=$all_words=$word_array=$all_words_count_array=$test_words_count_array=array();

		$jwc_params = new stdClass();  # for variables we will be carrying across to the template

		# get parameters
		$jwc_params->moduleclasssfx	= (trim($params->get('moduleclass_sfx'))!="") ? ' class="'.$params->get('moduleclass_sfx').'"' : ''; # version 1.1
		$jwc_var_scancurrentarticle	= ($params->get('paramSCANARTICLEONLY')*1)==1 ? true : false; # version 2.2
		$jwc_var_scancontenttype	= $params->get('paramSCANCONTENT')*1; # version 1.1
		$jwc_var_searchurl			= str_replace("&amp;", "&", trim($params->get('paramSEARCHURL'))); # version 1.1  # version 1.6.7
		$jwc_var_searchurl			= str_replace("&", "&amp;amp;", trim($jwc_var_searchurl)); # version 1.6.7  # version 3.1
		$jwc_var_minwordlength		= $params->get('paramMINWORDLENGTH')*1; # version 1.1
		$jwc_var_wordcount			= ($params->get('paramWORDCOUNT')*1)<=0?1:$params->get('paramWORDCOUNT')*1; # version 1.1
		$jwc_var_minfontsize		= $params->get('paramMINFONTSIZE')*1; # version 1.1
		$jwc_var_maxfontsize		= $params->get('paramMAXFONTSIZE')*1; # version 1.1
		$jwc_var_poweredby			= ($params->get('paramPOWEREDBY')*1)==1 ? true : false; # version 1.1
		$temp_array = array( 'left', 'center', 'right', 'justify' ); # version 1.2
		$jwc_params->textalignment	= $temp_array[($params->get('paramTEXTALIGNMENT')*1)]; # version 1.2
		$jwc_var_wordunderlines		= ($params->get('paramWORDUNDERLINES')*1)==1 ? "text-decoration:none;" : ""; # version 1.3
		$jwc_var_scank2				= ($params->get('paramSCANK2')*1)==1 ? true : false; # version 1.3
		$jwc_var_scanhotproperty	= ($params->get('paramSCANHOTPROPERTY')*1)==1 ? true : false; # version 1.3
		$jwc_var_casesensitive		= ($params->get('paramCASESENSITIVE')*1)==1 ? true : false; # version 1.5
		$jwc_var_excludekeywords	= trim($params->get('paramEXCLUDEKEYWORDS'));
		$jwc_var_excludekeywords	= ($jwc_var_excludekeywords!="") ? explode( ",", $jwc_var_excludekeywords ) : array(); # version 1.5
		for($i=0; $i<count($jwc_var_excludekeywords); $i++) { $jwc_var_excludekeywords[$i] = trim($jwc_var_excludekeywords[$i]); } # version 1.6.5
		$jwc_var_includekeywords	= trim($params->get('paramINCLUDEKEYWORDS'));
		$jwc_var_includekeywords	= ($jwc_var_includekeywords!="") ? explode( ",", $jwc_var_includekeywords ) : array(); # version 1.4
		for($i=0; $i<count($jwc_var_includekeywords); $i++) { $jwc_var_includekeywords[$i] = trim($jwc_var_includekeywords[$i]); } # version 1.6.5
		$jwc_var_scanphocadownloads	= ($params->get('paramSCANPHOCADOWNLOAD')*1)==1 ? true : false; # version 1.5
		$jwc_params->showdebug		= ($params->get('paramSHOWDEBUG')*1)==1 ? true : false; # version 1.5
		$jwc_var_enablecss3			= ($params->get('paramLINKBG')*1)==1 ? true : false; # version 1.6.1
		$jwc_var_linkbgcolor		= trim($params->get('paramLINKBGCOLOR')); # version 1.6.1
		$jwc_var_linkfgcolor		= trim($params->get('paramLINKFGCOLOR')); # version 1.6.1
		$jwc_var_cornerradius		= $params->get('paramLINKCORNERRADIUS')*1; # version 1.6.1
		# $jwc_var_dbmaxlength		= $params->get('paramCONTENTMAXLENGTH')*1; # version 1.6.1 # removed 16/06/2011
		$jwc_var_linkbgsize			= ($params->get('paramLINKBGSIZE')*1)==1 ? true : false; # version 1.6.1 # DEPRECATED
		# $jwc_var_linkpadding		= $params->get('paramLINKPADDING')*1; # version 1.6.1 # DEPRECATED
		$jwc_var_linkborder			= $params->get('paramLINKBORDER')*1; # version 1.6.1
		$jwc_var_linkbordercolor	= trim($params->get('paramLINKBORDERCOLOR')); # version 1.6.3
		$jwc_var_linkbordersize		= trim($params->get('paramLINKBORDERSIZE')); # version 1.6.3
		$jwc_params->modulewidth	= (trim($params->get('paramMODULEWIDTH'))!="") ? ' style="width:' . trim($params->get('paramMODULEWIDTH')) . ';"' : ''; # version 1.6.3
		$jwc_var_sortordertype		= ($params->get('paramSORTORDERTYPE'))*1; # version 1.6.4
		$jwc_var_sortorder			= ($params->get('paramSORTORDERORDER'))*1; # version 1.6.4
		$jwc_var_scancategoriesinc	= trim($params->get('paramINCLUDECATEGORIES'))!="" ? explode( ",", $params->get('paramINCLUDECATEGORIES') ) : array(); # version 1.6.4
		for($i=0; $i<count($jwc_var_scancategoriesinc); $i++) { $jwc_var_scancategoriesinc[$i] = trim($jwc_var_scancategoriesinc[$i]); } # version 1.6.5
		$jwc_var_scancategoriesexc	= trim($params->get('paramEXCLUDECATEGORIES'))!="" ? explode( ",", $params->get('paramEXCLUDECATEGORIES') ) : array(); # version 1.6.4
		for($i=0; $i<count($jwc_var_scancategoriesexc); $i++) { $jwc_var_scancategoriesexc[$i] = trim($jwc_var_scancategoriesexc[$i]); } # version 1.6.5
		$jwc_var_useaccesslevels	= ($params->get('paramUSEACCESSLEVELS')*1)==1 ? true : false; # version 1.6.4
		$jwc_var_scanjevents		= ($params->get('paramSCANJEVENTS')*1)==1 ? true : false; # version 1.6.7.1
//		$jwc_var_scanfind			= ($params->get('paramSCANAUTOFIND')*1)==1 ? true : false; # version 2.1.0
		$jwc_var_scannojoomla		= ($params->get('paramSCANNOJOOMLA')*1)==1 ? true : false; # version 2.1.1
		$autodebug_output			= ""; # version 2.1.0

		# get user details
		$user = JFactory::getUser();
		$usergrps = $user->get("groups");
		$subrowcount=$usergrpref=0;

		# modify sql query if using access levels
		if ($jwc_var_useaccesslevels) {

			# generate accesslevel array
			$accesslevels=array();
			$accesslevels[]=1;
			while(list($key, $value) = each($usergrps)) {
				$thislevel = $value;

				$db2 =& JFactory::getDBO();
				$db2->setQuery( "SELECT id, rules FROM `#__viewlevels` WHERE rules LIKE '%".$value."%'" );
				$rows2 = $db2->loadAssocList();

				for($j=0;$j<count($rows2);$j++) {
					$rule_id=$rows2[$j]['id'];
					$rules_array=explode(",", substr($rows2[$j]['rules'], 1, -1));
					if (in_array($rule_id, $rules_array))
						$accesslevels[] = $rows2[$j]['id'];
				}
				$subrowcount=count($rows2);
				$usergrpref++;
			}
			$sql_accessconditions = " AND a.`access` IN (" . implode(",", $accesslevels) . ")";
		}
		if (count($jwc_var_scancategoriesinc)>0) {
			$temp_catarray=array();
			for ($i=0; $i<count($jwc_var_scancategoriesinc); $i++) {
				$temp_catarray[]=trim($jwc_var_scancategoriesinc[$i]);
			}
			$sql_accessconditions.=($jwc_var_scancurrentarticle)&&($joomla_article_id==0)?"":" AND a.`catid` IN (" . implode(",", $temp_catarray) . ")";
		}
		if (count($jwc_var_scancategoriesexc)>0) {
			$temp_catarray=array();
			for ($i=0; $i<count($jwc_var_scancategoriesexc); $i++) {
				$temp_catarray[]=trim($jwc_var_scancategoriesexc[$i]);
			}
			$sql_accessconditions.=($jwc_var_scancurrentarticle)&&($joomla_article_id==0)?"":" AND a.`catid` NOT IN (" . implode(",", $temp_catarray) . ")";
		}

		# check inclusion list is empty
		if (count($jwc_var_includekeywords)<=0) {

			# convert to unicode
			$db =& JFactory::getDBO();
			$db->setQuery( "SET NAMES 'utf8'" );
			$temp_result = $db->query();

			# what to retrieve in SQL
			$temp_array = array( 'title', 'introtext', 'title, introtext' );
			$modulescansql = $temp_array[$jwc_var_scancontenttype];
			# as per parameter
			$temp_array = array( 'a.title', 'a.introtext', 'a.title, a.introtext' );
			$modulescancontentsql = $temp_array[$jwc_var_scancontenttype];

			$enable_integration = $jwc_var_scank2 || $jwc_var_scanhotproperty || $jwc_var_scanphocadownloads || $jwc_var_scanjevents;

			# generate sql query
			if (!$enable_integration){

				# article specific?  NOTE: 0 = not viewing an article so get words from overall content (eg. viewing the homepage)!
				$joomla_article_id = (JRequest::getVar('option')==='com_content' && JRequest::getVar('view')==='article')? JRequest::getInt('id') : 0;
				$articlespecific=($jwc_var_scancurrentarticle)&&($joomla_article_id>0)?' AND `id`='.$joomla_article_id:"";
				$modulescancontentsql=($jwc_var_scancurrentarticle)&&($joomla_article_id>0)?'a.`title`,CONCAT(a.`introtext`,a.`fulltext`) AS introtext':$modulescancontentsql;

				# no 3rd-party integration sql
				$sql_query = 'SELECT '.$modulescancontentsql.' FROM `#__content` a WHERE a.`state`=1'.$articlespecific.$sql_accessconditions;

			} else {

				# what to get from K2 articles
				$temp_array = array( 'b.title title', 'b.introtext introtext', 'b.title title, b.introtext introtext' );
				$modulescancontentk2 = $temp_array[$jwc_var_scancontenttype];
				$k2_sql=($jwc_var_scank2)?'UNION ALL SELECT '.$modulescancontentk2.' FROM `#__k2_items` b WHERE b.published=1':'';

				# what to get from HotProperty articles
				$temp_array = array( 'c.name title', 'c.intro_text introtext', 'c.name title, c.intro_text introtext' );
				$modulescancontenthp = $temp_array[$jwc_var_scancontenttype];
				$hp_sql=($jwc_var_scanhotproperty)?'UNION ALL SELECT '.$modulescancontenthp.' FROM `#__hp_properties` c WHERE c.published=1':'';

				# what to get from PhocaDownload articles
				$temp_array = array( 'd.title title', 'd.description introtext', 'd.title title, d.description introtext' );
				$modulescancontentphocadlsql = $temp_array[$jwc_var_scancontenttype];
				$pcdl_sql=($jwc_var_scanphocadownloads)?'UNION ALL SELECT '.$modulescancontentphocadlsql.' FROM `#__phocadownload` d WHERE d.published=1':'';

				# what to get from jEvents articles
				$temp_array = array( 'e.summary title', 'e.description introtext', 'e.summary title, e.description introtext' );
				$modulescancontentjeventssql = $temp_array[$jwc_var_scancontenttype];
				$je_sql=($jwc_var_scanjevents)?'UNION ALL (SELECT '.$modulescancontentjeventssql.' FROM `#__jevents_vevdetail` e INNER JOIN `#__jevents_vevent` e1 ON e1.id=e.id WHERE e.state=1':'';

				# correct access groups if 3rd-party used
				if ($jwc_var_useaccesslevels) {
					if ($jwc_var_scank2) {
						$temp_array=array();
						$temp_array[]=0;  // for adding public by default
						for ($i=0; $i<count($usergrps); $i++) {
							$temp_array[]=$usergrps[$i];
						}
						$k2_sql .= " AND b.access IN (" . implode(",", $temp_array) . ")";
					}
					if ($jwc_var_scanphocadownloads) {
						$temp_array=array();
						$temp_array[]=0;  // for adding public by default
						for ($i=0; $i<count($usergrps); $i++) {
							$temp_array[]=$usergrps[$i];
						}
						$pcdl_sql .= " AND d.access IN (" . implode(",", $temp_array) . ")";
					}
					if ($jwc_var_scanjevents) {
						$temp_array=array();
						$temp_array[]=0;  // for adding public by default
						for ($i=0; $i<count($usergrps); $i++) {
							$temp_array[]=$usergrps[$i];
						}
						$je_sql .= " AND e1.access IN (" . implode(",", $temp_array) . ")";
					}
				}
				$je_sql.=($jwc_var_scanjevents)?")":""; // add the closing bracket for the union

				# generate the final query
				$sql_query = 'SELECT '.$modulescansql.' FROM ( ';
				if ($jwc_var_scannojoomla) {
					$integration_sql=trim($k2_sql.' '.$hp_sql.' '.$pcdl_sql.' '.$je_sql);
					$integration_sql=(strlen($integration_sql)>0) ? substr($integration_sql, 10) : "";
					$sql_query .= trim(str_replace('  ', ' ', $integration_sql));
				} else {
					$sql_query.= trim(str_replace('  ', ' ', 'SELECT '.$modulescancontentsql.' FROM `#__content` a WHERE a.`state`=1 '.$k2_sql.' '.$hp_sql.' '.$pcdl_sql.' '.$je_sql));
				}
				$sql_query.= ' ) t1';
			}

			# run the sql query
			$db->setQuery( $sql_query );
			$rows = $db->loadAssocList();
			$all_words=array();
			$total_article_count = count($rows);

			# for debug
			$total_article_length=$total_article_formatted=$total_minlength_count=0;

			# get all words
			for($i=0;$i<count($rows);$i++) {
				$countvalidrows++;
				$title = ($jwc_var_scancontenttype != 1) ? $rows[$i]['title'] : '';
				$introtext = ($jwc_var_scancontenttype > 0)||($jwc_var_scancurrentarticle) ? $rows[$i]['introtext'] : '';
				$full_string = substr($title.' '.$introtext, 0, 100000);
				$total_article_length+=strlen(trim($full_string));
				$full_string = joes_return_keywords( $full_string );  // try to return only words bearing in mind International characters
				$total_article_formatted+=strlen(trim($full_string));
				$word_array = explode(" ", str_replace("  ", " ", $full_string));
				for($j=0;$j<count($word_array);$j++) {
					$total_word_count++;
					$cloud_word=trim($word_array[$j]);
					if ( joes_utf8_strlen($cloud_word) >= $jwc_var_minwordlength ) {
						$total_minlength_count++;
						if ($jwc_var_casesensitive) {
							$all_words[]=trim($cloud_word);
						} else {
							if (function_exists('mb_strtolower')) {
								$cloud_word=mb_strtolower($cloud_word, 'UTF-8');
								$all_words[]=trim($cloud_word);
							} else {
								$cloud_word=strtolower($cloud_word);
								$all_words[]=trim($cloud_word);
							}
						}
					}
				}
			}

			# generate unique keyword array and associated counts
			$all_words_count_array = array_count_values ( $all_words );		// create associative array with words as keys and frequency as values
			unset($all_words);												// clear some memory and we're going to reuse it
			unset($all_words_count_array['']);								// remove any words which are NULL
			arsort( $all_words_count_array );								// sort by values in reverse numerical order

			# for debug
			$total_unique_count = count($all_words_count_array);
			$sum_valid_words = $q_counter = 0;
			if ( $jwc_params->showdebug ){
				$q_counter++;
				foreach($all_words_count_array as $sumvalid_k => $sumvalid_v){
					$sum_valid_words+=($sumvalid_v * 1);
					if ( $q_counter >= $jwc_var_wordcount) break;
				}
			}

			# loop through recovered array of words
			$countvalidwords=0; $all_words=array();
			foreach($all_words_count_array as $key => $value) {
				$cloud_word=trim($key);

				if ((!in_array($cloud_word, $jwc_var_excludekeywords))&&(!in_array($cloud_word, $all_words))&&(trim($cloud_word)!="")) {
					$countvalidwords++;
					$all_words[]=$cloud_word;
					if ($jwc_params->showdebug) $test_words_count_array[]=$key.':'.$value; // for testing purposes: counts the words
				}

				# if reached maximum number of words to keep
				if ( $countvalidwords >= $jwc_var_wordcount ) break;

			}

			unset($all_words_count_array);

		} else {
			$all_words=$jwc_var_includekeywords;
			$countvalidwords=count($all_words);
		}

		$total_validreq_count=$countvalidwords;

		# work out font-size variances and add css styles
		$countvalidwords=($countvalidwords==0)?1:$countvalidwords;
		$fontsize_variant=(( $jwc_var_maxfontsize - $jwc_var_minfontsize )/$countvalidwords);
		$modulelinkbgcolor=(trim($jwc_var_linkbgcolor)!="")?$jwc_var_linkbgcolor:'#eee';
		$link_bg_css=($jwc_var_enablecss3)?'-moz-border-radius:'.$jwc_var_cornerradius.'px; -webkit-border-radius:'.$jwc_var_cornerradius.'px;padding:2px;margin:4px;':'';
		$link_bg_css_color=((trim($jwc_var_linkbgcolor)!="")&&$jwc_var_enablecss3)?'background-color:'.$jwc_var_linkbgcolor.';':'';
		$link_fg_css_color=((trim($jwc_var_linkfgcolor)!="")&&$jwc_var_enablecss3)?'color:'.$jwc_var_linkfgcolor.';':'';
		$link_border_css=((trim($jwc_var_linkborder)!="")&&$jwc_var_enablecss3&&(trim($jwc_var_linkborder)!=""))?'border:'.$jwc_var_linkbordersize.'px solid '.$jwc_var_linkbordercolor.';':'';

		# add the html code to each word
		$all_words_html=array();
		$count=0;
		$baseurl=JURI::base();
		for ($i=0; $i<count($all_words); $i++) {
			$count++;											// increment count of words that were valid
			$cloud_word=trim($all_words[$i]);

			# assign font size
			$fontsize=intval( $jwc_var_maxfontsize -($fontsize_variant*$count));

			# double-check search URL is valid XHTML | version 2.0.0
			$jwc_var_searchurl_encoded=html_entity_decode($jwc_var_searchurl);
			$jwc_var_searchurl = htmlentities(trim($jwc_var_searchurl_encoded));

			$all_words_html[]=$cloud_word.'|:joe:|<a href="'.$baseurl.trim($jwc_var_searchurl).$cloud_word.'" style="font-size:'.$fontsize.'px;line-height:'.($fontsize+2).'px;'.$jwc_var_wordunderlines.$link_bg_css.$link_bg_css_color.$link_fg_css_color.$link_border_css.'">'.$cloud_word.'</a> &nbsp;';
		}

		# sort final words
		if ($jwc_var_sortordertype == 0) {
			shuffle( $all_words_html );
		} else if ($jwc_var_sortordertype == 1) {
			sort( $all_words_html , SORT_STRING );
			$all_words_html=array_reverse( $all_words_html );  // reverse this so it is in descending order
		} else if ($jwc_var_sortordertype == 1) {
			$all_words_html=array_reverse( $all_words_html ); //already in desc frequency order but reverse so next option applies
		}

		# what order?
		if ($jwc_var_sortorder == 1) {
			$all_words_html=array_reverse( $all_words_html );
		}

		# remove prefixing order value
		for ($i=0; $i<count($all_words_html); $i++) {
				$temp_array=explode("|:joe:|", $all_words_html[$i]);
				$all_words_html[$i]=$temp_array[1];
		}

		# generate the main visible content and separate so it stands out in the HTML source
		$jwc_params->modulecontent="\n\n\n".implode( " ", $all_words_html )."\n\n\n";

		# further module options
		$jwc_params->modulecontent.=($jwc_var_poweredby)?' <a href="http://www.joellipman.com/" target="_blank" style="font-size:8px;'.$jwc_var_wordunderlines.$link_bg_css.$link_bg_css_color.$link_fg_css_color.$link_border_css.'">JoelLipman.Com</a>':"";

		# debug mode
		$jwc_params->debug = "";
		if ($jwc_params->showdebug){

			# for debug
			$autodebug_output.="<pre>************ START DEBUG NOTES ************\n// Diagnosis\n";
			$autodebug_output.="Module Version: 3.2.1\n";
			$autodebug_output.=(count($jwc_var_includekeywords)>0)?"INCLUSION LIST OVERRIDE: No automation available.\n":"";
			$autodebug_output.="Article ID: ".$joomla_article_id."\n";
			$autodebug_output.="Article Count - Total: ".$total_article_count."\n";
			$autodebug_output.="Article Length - Total: ".$total_article_length."\n";
			$autodebug_output.="Article Length - Formatted Total: ".$total_article_formatted."\n";
			$autodebug_output.="Word Count - Unique: ".$total_unique_count."\n";
			$autodebug_output.="Word Count - Valid (Min Length): ".$total_minlength_count."\n";
			$autodebug_output.="Word Count - Valid (Requested): ".$total_validreq_count."\n";
			$autodebug_output.="Word Count - Total: ".$total_word_count."\n";
			$autodebug_output.="\n";
			$autodebug_output.="[".$sql_query."]\n";
			$autodebug_output.="\n";
			$autodebug_output.=(count($jwc_var_includekeywords)>0)?"// Inclusion List\n".var_export($jwc_var_includekeywords, true)."\n":"";
			$autodebug_output.="// Processed Words\n".var_export($test_words_count_array, true)."\n";
			$autodebug_output.="// Module Parameters\n".var_export($params, true)."\n";
			$autodebug_output.="// User Groups\n".var_export($usergrps, true)."\n";
			$autodebug_output.="************ END DEBUG NOTES ************</pre>\n";
			$jwc_params->debug = $autodebug_output;
		}

		return $jwc_params;
    }
}
?>
