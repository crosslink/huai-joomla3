<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

$tag = $params->get('tag_id');
$tag = $tag ? 'id="'.$tag.'"' : NULL;
?>
<div class="mobile-menu">
<select size="1" class="menu<?php echo $class_sfx;?> xtcmobilemenu" <?php echo $tag; ?> onchange="location.href=this.value">
<?php
	foreach ($list as $i => &$item) {
		$selected = ($item->id == $active_id) ? 'selected="selected"' : NULL;
		echo '<option value="'.$item->flink.'" '.$selected.'>'.$item->title.'</option>';
	}
?>
</select>
</div>