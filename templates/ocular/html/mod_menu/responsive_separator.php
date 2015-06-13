<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ?
		$linktype = '<img src="'.$item->menu_image.'" alt="'.cleanXmenu($item->title).'" /><span class="image-title">'.cleanXmenu($item->title).'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.cleanXmenu($item->title).'" />';
}
else { $linktype = parseXmenu($item->title);
}

?><span class="separator"><?php echo $title; ?><?php echo $linktype; ?></span>