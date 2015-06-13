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

        $left8colgrid 	= $gridParams->left8width;

        $right8colgrid	= $gridParams->right8width;

        if ($this->countModules('left8') == 0){

         $left8colgrid  = "0";

        }



        if ($this->countModules('right8') == 0){

         $right8colgrid  = "0";

        }



        $left8 = $this->countModules( 'left8' );

	$right8 = $this->countModules( 'right8' );



        $areaWidth =  100;

	$order = 'user37,user38,user39,user40,user41,user42';

	$columnArray = array(

	        'user37' => '<jdoc:include type="modules" name="user37" style="xtc" />',

		'user38' => '<jdoc:include type="modules" name="user38" style="xtc" />',

		'user39' => '<jdoc:include type="modules" name="user39" style="xtc" />',

		'user40' => '<jdoc:include type="modules" name="user40" style="xtc" />',

		'user41' => '<jdoc:include type="modules" name="user41" style="xtc" />',

		'user42' => '<jdoc:include type="modules" name="user42" style="xtc" />'

	);



	$columnClass = '';

	$debug = 0;

	$user37_42 = xtcBootstrapGrid($columnArray,$order,'',$columnClass);

$r8wrapclass = $gridParams->r8width ? 'xtc-bodygutter' : '';
$r8class = $gridParams->r8width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r8pad = $gridParams->r8width ? 'xtc-wrapperpad' : '';
echo '<a id="region8anchor" class="moveit"></a>';
	if ($left8 || $user37_42 || $right8) {

        echo '<div id="region8wrap" class="'.$r8wrapclass.'">';

        echo '<div id="region8pad" class="'.$r8pad.'">';

	echo '<div id="region8" class="row-fluid '.$r8class.'">';



        if ($left8) { echo '<div id="left8" class="span'.$left8colgrid.'"><jdoc:include type="modules" name="left8" style="xtc" /></div>';}
        
        if ($user37_42) {

	echo '<div class="span'.(12-$left8colgrid-$right8colgrid).'">';

        

        if ($user37_42) { echo '<div id="user37_42" class="clearfix">'.$user37_42.'</div>'; }

	echo '</div>';
        }

	if ($right8) { echo '<div id="right8" class="span'.$right8colgrid.'"><jdoc:include type="modules" name="right8" style="xtc" /></div>';}

	echo '</div>';

        echo '</div>';

	echo '</div>';

	}