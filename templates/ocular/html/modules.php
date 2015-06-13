<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

function modChrome_xtc($module, $params, $attribs) {
	$content         = $module->content;
	$suffix          = $params->get('moduleclass_sfx', '');
    if ($module->showtitle)  {
			$moduleClass = 'title-on';
		} else { 
	    $moduleClass = 'title-off';
    }


?>
<div class="module <?php echo $moduleClass;?>  <?php echo  $suffix; ?>">
<?php if ($module->showtitle != 0) { ?>
  <?php $modtitle = explode("||", $module->title, 2); ?>
  <h3 class="moduletitle">
    <?php if(!empty($modtitle[0])) { ?><span class="first_word"><?php echo $modtitle[0];?></span><?php } ?>
    <?php if(!empty($modtitle[1])) {?><span class="rest"><?php echo $modtitle[1]; ?></span><?php }?>
</h3>

<?php } ?>
  <div class="modulecontent"> <?php echo $content; ?> </div>
</div>
<?php }