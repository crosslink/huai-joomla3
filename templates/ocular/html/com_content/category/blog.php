<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Leading articles are custom-fit in 3 columns
switch (trim($this->pageclass_sfx)) {
	case 'leftlarge': $targetitem = 0; $defaultspan = 'span3'; break;
  case 'centerlarge': $targetitem = 1; $defaultspan = 'span3'; break;
  case 'rightlarge': $targetitem = 2; $defaultspan = 'span3'; break;
  default: $targetitem = -1; $defaultspan = 'span12'; break; //Normal layout (full width)
}   
?>


<div class="blog-featured <?php echo $this->pageclass_sfx;?>">
<div class="itemListCategory">
	<?php if ( $this->params->get('show_page_heading')!=0) : ?>
		<h1 class="pagetitle">
			<span>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</span>
		</h1>
	<?php endif; ?>
    <?php if ($this->params->get('show_category_title', 1) || $this->params->get('page_subheading') || $this->params->get('show_description') || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1) || $this->params->get('show_description_image') && $this->category->getParams()->get('image') || $this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
<div class="categorytop">



<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>

	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
    	 <div class="catImageContainer">
		<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
        	<div class="clr"></div>
	</div>
	<?php endif; ?>
	

<?php endif; ?>
</div>
<?php endif; ?>

<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
	<?php if ($this->params->get('show_no_articles', 1)) : ?>
		<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>
<?php endif; ?>
</div>

<div style="clear:both;"></div>

	<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
    <div class="jcatHeader">
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading')); ?>
		<?php if ($this->params->get('show_category_title')) : ?>
			<?php echo $this->category->title;?>
		<?php endif; ?>
	</h2>
	
<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<p><?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?></p>
	<?php endif; ?>
</div>
<?php endif; ?>
    
	<?php
		if (!empty($this->lead_items)) {
			$rows = array_chunk($this->lead_items, 3);
			foreach ($rows as $row) {
				echo '<div class="items-leading xtc-leading row-fluid">';
				foreach($row as $count => $item) {
					$class = $count == $targetitem ? 'span6' : $defaultspan;
					echo '<div class="'.$class.'">';
					$this->item = &$item;
					echo $this->loadTemplate('item');
					echo '</div>';
				}
			  echo '</div>';
			}
		}

		$introcount=(count($this->intro_items));
		$counter=0;
    $count = 1;
	?>
	<?php if (!empty($this->intro_items)) : ?>
		<div class="xtc-intro clearfix row-fluid">
			<?php
				$leadingcount=0;
				foreach ($this->intro_items as $key => &$item) :
				$key= ($key-$leadingcount)+1;
				$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
				$row = $counter / $this->columns ;
				if ($counter % $this->columns == 0){
					$item_order = 'gridfirst';
				}elseif ($counter % $this->columns == $this->columns -1){
					$item_order='gridlast';
				}else{
					$item_order='';
				}
        $customSpans = '';
        $cols = $this->columns;
				$spaces = 12; $cs = 0;
				if (is_array($customSpans)) {
					$cs = count($customSpans);
					foreach ($customSpans as $c => $s) { $spaces -= intval($s); }
				}
	
				$spanClass = floor($spaces / ($cols - $cs));
				if ($spanClass == 0) $spanClass = 1;
		    if ($count%$this->columns == 1) {  
         //echo '<div class="row-fluid"><div class="span12">';
		    }
			?>


			<div class="<?php echo $item_order ?> span<?php echo $spanClass;?> xtc-category-col cols-<?php echo (int) $this->columns;?> item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
			</div>

			<?php
		    if ($count%$this->columns == 0) {
        //echo '</div></div>';
		    }
		    $count++;
			?>     
			<?php $counter++; ?>		
			<?php endforeach; ?>
		</div> 
	<?php endif; ?>
  
	<?php if (!empty($this->link_items)) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3>
				<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
			</h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>

	<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="Pagination">
			<?php  if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>

			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php  endif; ?>
</div>