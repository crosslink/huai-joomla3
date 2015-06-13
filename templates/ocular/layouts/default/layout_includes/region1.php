<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

defined('_JEXEC') or die;

$centerWidth = $tmplWidth;;	
$areaWidth =  $centerWidth;
$order = 'top1,top2,top3,top4,top5,top6';
$columnArray = array(
	'top1' => '<jdoc:include type="modules" name="top1" style="xtc" />',
	'top2' => '<jdoc:include type="modules" name="top2" style="xtc" />',
	'top3' => '<jdoc:include type="modules" name="top3" style="xtc" />',
	'top4' => '<jdoc:include type="modules" name="top4" style="xtc" />',
	'top5' => '<jdoc:include type="modules" name="top5" style="xtc" />',
	'top6' => '<jdoc:include type="modules" name="top6" style="xtc" />'
);

$customWidths = '';
$customSpans = '';
$columnClass = '';
$columnPadding = '';
$debug = 0;
$top1_6 = xtcBootstrapGrid($columnArray,$order,$customSpans,$columnClass,$debug);

$r1wrapclass = $gridParams->r1width ? 'xtc-bodygutter' : '';
$r1class = $gridParams->r1width ? 'xtc-wrapper' : 'xtc-fluidwrapper';
$r1pad = $gridParams->r1width ? 'xtc-wrapperpad' : '';
if ($this->countModules('inset') || $top1_6) {
?>
<a id="region1anchor" class="moveit"></a>

	<div id="region1wrap" class="<?php echo $r1wrapclass; ?>">
		<div id="region1pad" class="<?php echo $r1pad; ?>">
			<div id="region1" class="row-fluid <?php echo $r1class; ?>">
				<jdoc:include type="modules" name="inset" style="xtc" />
               <?php if ($top1_6) {	echo $top1_6;} ?>
			</div>
		</div>
		<div id="r1separator"></div>
	</div>
<?php
}