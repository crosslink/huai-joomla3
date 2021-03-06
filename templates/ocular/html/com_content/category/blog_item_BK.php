<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
$canEdit	= $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');


?>
<div class="cat-item">
<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>




  
  
  
                    <div class="blog-post">
                      
                      
   <?php  if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
	<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>
	<div class="img-intro-<?php echo htmlspecialchars($imgfloat); ?>">
	<img
		<?php if ($images->image_intro_caption):
			echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
		endif; ?>
		src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
	</div>
<?php endif; ?>


 </div>
  
  
  
   
	





<?php if ($params->get('show_title')) {?>
	<h2 class="cat_title">
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
			<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
			<?php echo $this->escape($this->item->title); ?>
            </a>
		<?php else : ?>
			<?php echo $this->escape($this->item->title); ?>
		<?php endif; ?>
	</h2>
<?php } ?>	


<?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) { ?>
	<div class="imagepe">
		<?php if ($params->get('show_print_icon')) { ?>
		<span class="print-icon">
			<?php echo JHtml::_('icon.print_popup', $this->item, $params); ?>
		</span>
		<?php } ?>
		<?php if ($params->get('show_email_icon')) { ?>
		<span class="email-icon">
			<?php echo JHtml::_('icon.email', $this->item, $params); ?>
		</span>
		<?php } ?>
		<?php if ($canEdit) : ?>
		<span class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $params); ?>
		</span>
		<?php endif; ?>
	</div>
<?php } ?>


<?php echo $this->item->event->beforeDisplayContent; ?>




<?php if (!$params->get('show_intro')) : ?>
	<?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>




<?php if ($params->get('show_category')) : ?>
		<div class="catItemCategory">
			<?php $title = $this->escape($this->item->category_title);
					$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catid)) . '">' . $title . '</a>'; ?>
			<?php if ($params->get('link_category')) : ?>
				<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo ($title); ?>
			<?php endif; ?>
		</div>
<?php endif; ?>

<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
<div class="catItemAuthor">
		<?php $author =  $this->item->author; ?>
		<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>

			<?php if (!empty($this->item->contactid ) &&  $params->get('link_author') == true):?>
				<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
				 JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid), $author)); ?>

			<?php else :?>
				<?php echo ($author); ?>
			<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ($params->get('show_parent_category') && $this->item->parent_id != 1) : ?>
		<div class="catItemDateCreated">
			<?php $title = $this->escape($this->item->parent_title);
				$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_id)) . '">' . $title . '</a>'; ?>
			<?php if ($params->get('link_parent_category')) : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
			<?php endif; ?>
		</div>
<?php endif; ?>

<?php if ($params->get('show_create_date')) : ?>
		<div class="catItemDateCreated">
		<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		</div>
<?php endif; ?>

<?php if ($params->get('show_publish_date')) : ?>
	<div class="catItemDateCreated">
		<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</div>
<?php endif; ?>

<?php if ($params->get('show_hits')) : ?>
		<div class="catItemDateCreated">
		<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
		</div>
<?php endif; ?>






<?php if ($params->get('show_intro')) : ?>   
<div class="catItemIntroText">
<?php 
echo $this->item->introtext; 
?>
</div>
<?php endif; ?>
	<?php if ($params->get('show_modify_date')) : ?>
<span class="catItemDateCreated">
		<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
</span>
<?php endif; ?>
<?php if ($params->get('show_readmore') && $this->item->readmore) {
	if ($params->get('access-view')) :
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
	else :
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
		$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		$link = new JURI($link1);
		$link->setVar('return', base64_encode($returnURL));
	endif;
?>
		<div class="readmore1">
        		<a class="btn"  href="<?php echo $link; ?>">
                <span style="padding-right:8px;"></span>
					<?php if (!$params->get('access-view')) :
						echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
					elseif ($readmore = $this->item->alternative_readmore) :
						echo $readmore;
						if ($params->get('show_readmore_title', 0) != 0) :
						    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						endif;
					elseif ($params->get('show_readmore_title', 0) == 0) :
						echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
					else :
						echo JText::_('COM_CONTENT_READ_MORE');
						echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					endif; ?></a>
		</div>
<?php } ?>



<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>
<?php echo $this->item->event->afterDisplayContent; ?>

</div>