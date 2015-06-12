<?php

defined ( '_JEXEC' ) or die ();
?>
<div id="Kunena" class="layout container-fluid">
<?php
if ($this->ktemplate->params->get('displayMenu', 1)) {
	$this->displayMenu ();
}
$this->displayLoginBox ();
$this->displayBreadcrumb ();

// Display current view/layout
$this->displayLayout();



?>
</div>
