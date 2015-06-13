<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div id="joeswordcloud"<?php echo $joeswordcloud_params->moduleclasssfx.$joeswordcloud_params->modulewidth; ?>>
	<p style="text-align:<?php echo $joeswordcloud_params->textalignment; ?>">
	<?php
		echo $joeswordcloud_params->modulecontent;
	?>
	</p>
</div>
<?php
	if ($joeswordcloud_params->showdebug) {
		echo $joeswordcloud_params->debug;
	}
?>