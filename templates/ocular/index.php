<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

// Call XTC framework
require JPATH_THEMES.'/'.$this->template.'/XTC/XTC.php';

// Load template parameters
$templateParameters = xtcLoadParams();

// Get the selected layout
$layout = $templateParameters->templateLayout;

// Call layout from layouts folder to create HTML

if ($xtc->agent->isMobile && $templateParameters->mobiledetect) {
	require JPATH_THEMES.'/'.$this->template.'/layouts/mobile/layout.php';
}
else {
	require JPATH_THEMES.'/'.$this->template.'/layouts/'.$layout.'/layout.php';
}