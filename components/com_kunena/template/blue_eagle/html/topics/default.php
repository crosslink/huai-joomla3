<?php

defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
?>
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>
<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_TOPICS')?>
		</td>



		<td class="klist-jump-all hidden-phone"><?php $this->displayForumJump () ?></td>

		<td class="klist-pages-all"><?php echo $this->getPagination ( 5 ); ?></td>
	</tr>
</table>

<?php $this->displayTemplateFile('topics', 'default', 'embed'); ?>

<table class="klist-actions">
	<tr>
		<td class="klist-actions-info-all">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_TOPICS')?>
		</td>
		<td class="klist-pages-all"><?php echo $this->getPagination ( 5 ); ?></td>
	</tr>
</table>

<?php
$this->displayWhoIsOnline ();
$this->displayStatistics ();
?>
