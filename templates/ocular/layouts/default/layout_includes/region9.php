<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

$centerWidth = $tmplWidth;

        $left9colgrid 	= $gridParams->left9width;

        $right9colgrid	= $gridParams->right9width;

        if ($this->countModules('left9') == 0){

         $left9colgrid  = "0";

        }



        if ($this->countModules('right9') == 0){

         $right9colgrid  = "0";

        }



        $left9 = $this->countModules( 'left9' );

	$right9 = $this->countModules( 'right9' );



        $areaWidth =  100;

	$order = 'user43,user44,user45,user46,user47,user48';

	$columnArray = array(

	        'user43' => '<jdoc:include type="modules" name="user43" style="xtc" />',

		'user44' => '<jdoc:include type="modules" name="user44" style="xtc" />',

		'user45' => '<jdoc:include type="modules" name="user45" style="xtc" />',

		'user46' => '<jdoc:include type="modules" name="user46" style="xtc" />',

		'user47' => '<jdoc:include type="modules" name="user47" style="xtc" />',

		'user48' => '<jdoc:include type="modules" name="user48" style="xtc" />'

	);



	$columnClass = '';

	$debug = 0;

	$user43_48 = xtcBootstrapGrid($columnArray,$order,'',$columnClass);

$r9wrapclass = $gridParams->r9width ? 'xtc-bodygutter' : '';
$r9class = $gridParams->r9width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r9pad = $gridParams->r9width ? 'xtc-wrapperpad' : '';
echo '<a id="region9anchor" class="moveit"></a>';
	if ($left9 || $user43_48 || $right9) {

        echo '<div id="region9wrap" class="'.$r9wrapclass.'">';

        echo '<div id="region9pad" class="'.$r9pad.'">';

	echo '<div id="region9" class="row-fluid '.$r9class.'">';



        if ($left9) { echo '<div id="left9" class="span'.$left9colgrid.'"><jdoc:include type="modules" name="left9" style="xtc" /></div>';}
        
        if ($user43_48) {

	echo '<div class="span'.(12-$left9colgrid-$right9colgrid).'">';

        

        if ($user43_48) { echo '<div id="user43_48" class="clearfix">'.$user43_48.'</div>'; }

	echo '</div>';
        }

	if ($right9) { echo '<div id="right9" class="span'.$right9colgrid.'"><jdoc:include type="modules" name="right9" style="xtc" /></div>';}

	echo '</div>';

        echo '</div>';

	echo '</div>';

	}