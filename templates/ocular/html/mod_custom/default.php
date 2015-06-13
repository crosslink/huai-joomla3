<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

?>


<?php if ($params->get('backgroundimage')): ?> <div style="background-image:url(<?php echo $params->get('backgroundimage');?>)"><?php endif;?> 
	<?php echo $module->content;?>
<?php if ($params->get('backgroundimage')): ?></div><?php endif;