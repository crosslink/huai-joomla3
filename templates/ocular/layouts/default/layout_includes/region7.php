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

        $left7colgrid 	= $gridParams->left7width;

        $right7colgrid	= $gridParams->right7width;

        if ($this->countModules('left7') == 0){

         $left7colgrid  = "0";

        }



        if ($this->countModules('right7') == 0){

         $right7colgrid  = "0";

        }



        $left7 = $this->countModules( 'left7' );

	$right7 = $this->countModules( 'right7' );



        $areaWidth =  100;

	$order = 'user31,user32,user33,user34,user35,user36';

	$columnArray = array(

	        'user31' => '<jdoc:include type="modules" name="user31" style="xtc" />',

		'user32' => '<jdoc:include type="modules" name="user32" style="xtc" />',

		'user33' => '<jdoc:include type="modules" name="user33" style="xtc" />',

		'user34' => '<jdoc:include type="modules" name="user34" style="xtc" />',

		'user35' => '<jdoc:include type="modules" name="user35" style="xtc" />',

		'user36' => '<jdoc:include type="modules" name="user36" style="xtc" />'

	);



	$columnClass = '';

	$debug = 0;

	$user31_36 = xtcBootstrapGrid($columnArray,$order,'',$columnClass);

$r7wrapclass = $gridParams->r7width ? 'xtc-bodygutter' : '';
$r7class = $gridParams->r7width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r7pad = $gridParams->r7width ? 'xtc-wrapperpad' : '';
echo '<a id="region7anchor" class="moveit"></a>';
	if ($left7 || $user31_36 || $right7) {

        echo '<div id="region7wrap" class="'.$r7wrapclass.'">';

        echo '<div id="region7pad" class="'.$r7pad.'">';

	echo '<div id="region7" class="row-fluid '.$r7class.'">';



        if ($left7) { echo '<div id="left7" class="span'.$left7colgrid.'"><jdoc:include type="modules" name="left7" style="xtc" /></div>';}
        if ($user31_36) {
	echo '<div class="span'.(12-$left7colgrid-$right7colgrid).'">';

        

        if ($user31_36) { echo '<div id="user31_36" class="clearfix">'.$user31_36.'</div>'; }

	echo '</div>';
}
	if ($right7) { echo '<div id="right7" class="span'.$right7colgrid.'"><jdoc:include type="modules" name="right7" style="xtc" /></div>';}

	echo '</div>';

        echo '</div>';

	echo '</div>';

	}