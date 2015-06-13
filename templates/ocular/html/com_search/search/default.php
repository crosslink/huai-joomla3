<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

if ($this->params->get('show_page_heading')) : ?>
<center><h1 class="pagetitle">
	<?php if ($this->escape($this->params->get('page_heading'))) :?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1></center>
<?php endif; ?>

<div align="center">
	<div class="formwrap">
		<div class="search<?php echo $this->pageclass_sfx; ?>">
			<?php echo $this->loadTemplate('form'); ?>
			<?php
				if ($this->error==null && count($this->results) > 0) :
					echo $this->loadTemplate('results');
				else :
					echo $this->loadTemplate('error');
				endif;
			?>
		</div>
	</div>
</div>