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
	<ul class="kpost-profile">
		<li class="kpost-username">
			<?php echo $this->profile->getLink() ?>
		</li>
		<?php if (!empty($this->usertype)) : ?>
		<li class="kpost-usertype">
			<span class = "kmsgusertype">( <?php echo JText::_($this->usertype) ?> )</span>
		</li>
		<?php endif ?>
		<?php $avatar = $this->profile->getAvatarImage ('kavatar', 'post'); if ($avatar) : ?>
		
		<div class="klatest-post-info">
			<span  class="klist-avatar"><?php echo $this->profile->getLink( $avatar ); ?></span>	</div>
	
		<?php endif; ?>

		<?php if ($this->profile->exists()): ?>

	

		<?php if (!empty($this->userranktitle)) : ?>
		<li class="kpost-userrank">
			<?php echo $this->escape($this->userranktitle) ?>
		</li>
		<?php endif ?>
		<?php if (!empty($this->userrankimage)) : ?>
		<li class="kpost-userrank-img">
			<?php echo $this->userrankimage ?>
		</li>
		<?php endif ?>

		<?php if (!empty($this->personalText)) : ?>
		<li class="kpost-personal">
			<?php echo $this->personalText ?>
		</li>
		<?php endif ?>

		



		<li class="kpost-smallicons">


			<?php echo $this->profile->profileIcon('location'); ?>
			<?php echo $this->profile->profileIcon('website'); ?>
			<?php echo $this->profile->profileIcon('private'); ?>
			<?php echo $this->profile->profileIcon('email'); ?>
		</li>

		<?php endif ?>
</ul>
