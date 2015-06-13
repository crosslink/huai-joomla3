<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

$centerWidth = $tmplWidth;;	
$areaWidth =  $centerWidth;
$order = 'bottom1,bottom2,bottom3,bottom4,bottom5,bottom6';
$columnArray = array(
	'bottom1' => '<jdoc:include type="modules" name="bottom1" style="xtc" />',
	'bottom2' => '<jdoc:include type="modules" name="bottom2" style="xtc" />',
	'bottom3' => '<jdoc:include type="modules" name="bottom3" style="xtc" />',
	'bottom4' => '<jdoc:include type="modules" name="bottom4" style="xtc" />',
	'bottom5' => '<jdoc:include type="modules" name="bottom5 style="xtc" />',
	'bottom6' => '<jdoc:include type="modules" name="bottom6" style="xtc" />'
);

$customWidths = '';
$customSpans = '';
$columnClass = '';
$columnPadding = '';
$debug = 0;
$bottom1_6 = xtcBootstrapGrid($columnArray,$order,$customSpans,$columnClass,$debug);

$r10wrapclass = $gridParams->r10width ? 'xtc-bodygutter' : '';
$r10class = $gridParams->r10width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r10pad = $gridParams->r10width ? 'xtc-wrapperpad' : '';	

echo '<a id="region10anchor" class="moveit"></a>';
if ($bottom1_6) {
	echo '<div id="region10wrap" class="'.$r10wrapclass.'">';
	echo '<div id="region10pad" class="'.$r10pad.'">';
	echo '<div id="region10" class="row-fluid '.$r10class.'">';
	
	echo $bottom1_6;
	echo '</div>';
	echo '</div>';
	echo '</div>';
}