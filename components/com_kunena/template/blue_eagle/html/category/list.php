<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
?>
<!-- Module position: kunena_announcement -->
<?php $this->displayModulePosition ( 'kunena_announcement' ) ?>


<?php
if (count ( $this->categories )) {
	$this->displayTemplateFile('category', 'list', 'embed');
} else {
	$this->displayInfoMessage ();
}
$this->displayWhoIsOnline();
$this->displayStatistics();
?>
