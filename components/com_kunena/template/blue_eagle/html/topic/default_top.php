<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>







<table class="<?php echo $this->class ?>">

<tbody>

	
	
<tr>
			
<td class="kmessage-top">
				
<?php $this->displayMessageContents() ?>
			
</td>
		
</tr>
	
	
<tr>

<td class="kbuttonbar-top">
				
<?php $this->displayMessageActions() ?>
			
</td>
		
</tr>

	
</tbody>

</table>


<!-- Begin: Message Module Position -->
<?php $this->displayModulePosition('kunena_msg_' . $this->mmm) ?>

<!-- Finish: Message Module Position -->
