<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

$doc = JFactory::getDocument();
?>
<div id="sb-searchwrap">
	<div id="sb-search" class="sb-search">
		<form action="<?php echo JRoute::_('index.php');?>" method="post">
	
			<input class="sb-search-input" placeholder="<?php echo Jtext::_('SEARCH'); ?>" name="searchword" maxlength="<?php echo $maxlength; ?>"  class="inputbox<?php echo $moduleclass_sfx; ?>" type="text" size="<?php echo $width; ?>" value="<?php echo $text; ?>"  onblur="if (this.value=='') this.value='<?php echo $text; ?>';" onfocus="if (this.value=='<?php echo $text; ?>') this.value='';" />
			<input class="sb-search-submit" type="submit" value="">
			<span class="sb-icon-search"><i class="icon-search"> </i></span>
	
			<input type="hidden" name="task" value="search" />
			<input type="hidden" name="option" value="com_search" />
			<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
		</form>
	</div>
</div>
<script src="<?php echo Juri::root(); ?>templates/ocular/html/mod_search/classie.js"></script>
<script src="<?php echo Juri::root(); ?>templates/ocular/html/mod_search/uisearch.js"></script>
<script>
	new UISearch( document.getElementById( 'sb-search' ) );
</script>