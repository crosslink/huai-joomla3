<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="weblink-category<?php echo $this->pageclass_sfx;?>">
<center>
<?php if($this->params->get('show_category_title', 1)) : ?>
<h1 class="pagetitle">
	<?php echo JHtml::_('content.prepare', $this->category->title, '', 'com_weblinks.category'); ?></span>
</h1>
<div class="vmformwrap">
<?php endif; ?>
<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_weblinks.category'); ?>
	<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>
<?php echo $this->loadTemplate('items'); ?>
<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
	<div class="cat-children">
	<h2 class="title"><?php echo JText::_('JGLOBAL_SUBCATEGORIES') ; ?></h2>
	<?php echo $this->loadTemplate('children'); ?>
	</div>
<?php endif; ?>
</div></div></center>