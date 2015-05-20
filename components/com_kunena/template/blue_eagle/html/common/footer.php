<?php

defined ( '_JEXEC' ) or die ();
?>
<!-- Module position: kunena_bottom -->
<?php $this->displayModulePosition( 'kunena_bottom' ); ?>
<?php if (isset($this->rss)) : ?>
<div class="krss-block"><?php echo $this->rss; ?></div>
<?php endif; ?>
