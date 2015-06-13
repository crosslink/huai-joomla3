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
$left2colgrid 	= $gridParams->left2width;
$right2colgrid	= $gridParams->right2width;
if ($this->countModules('left2') == 0){
	$left2colgrid  = "0";
}

if ($this->countModules('right2') == 0){
	$right2colgrid  = "0";
}
$left2 = $this->countModules( 'left2' );
$right2 = $this->countModules( 'right2' );

$areaWidth =  100;
$order = 'user1,user2,user3,user4,user5,user6';
$columnArray = array(
	'user1' => '<jdoc:include type="modules" name="user1" style="xtc" />',
	'user2' => '<jdoc:include type="modules" name="user2" style="xtc" />',
	'user3' => '<jdoc:include type="modules" name="user3" style="xtc" />',
	'user4' => '<jdoc:include type="modules" name="user4" style="xtc" />',
	'user5' => '<jdoc:include type="modules" name="user5" style="xtc" />',
	'user6' => '<jdoc:include type="modules" name="user6" style="xtc" />'
);

$customWidths = '';
$columnClass = '';
$columnPadding = '';
$debug = 0;
$user1_6 = xtcBootstrapGrid($columnArray,$order,'',$columnClass,$debug);

$r2wrapclass = $gridParams->r2width ? 'xtc-bodygutter' : '';
$r2class = $gridParams->r2width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r2pad = $gridParams->r2width ? 'xtc-wrapperpad' : '';
echo '<a id="region2anchor" class="moveit"></a>';
if ($left2 || $user1_6 || $right2) {
	echo '<div id="region2wrap" class="'.$r2wrapclass.'">';
  echo '<div id="region2pad" class="'.$r2pad.'">';
	echo '<div id="region2" class="row-fluid '.$r2class.'">';
  if ($left2) { echo '<div id="left2" class="span'.$left2colgrid.'"><jdoc:include type="modules" name="left2" style="xtc" /></div>';}
	if ($user1_6) { echo '<div id="user1_6" class="span'.(12-$left2colgrid-$right2colgrid).'">'.$user1_6.'</div>'; }
	if ($right2) { echo '<div id="right2" class="span'.$right2colgrid.'"><jdoc:include type="modules" name="right2" style="xtc" /></div>';}
	echo '</div>';
	echo '</div>';
	echo '</div>';
}