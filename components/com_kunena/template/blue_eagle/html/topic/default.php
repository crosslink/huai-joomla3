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
<?php
$this->document->addScriptDeclaration('// <![CDATA[
var kunena_anonymous_name = "'.JText::_('COM_KUNENA_USERNAME_ANONYMOUS', true).'";
// ]]>');
?>

<?php if ($this->category->headerdesc) : ?>
	<div id="kforum-head" class="<?php echo isset ( $this->category->class_sfx ) ? ' kforum-headerdesc' . $this->escape($this->category->class_sfx) : '' ?>">
		<?php echo KunenaHtmlParser::parseBBCode ( $this->category->headerdesc ) ?>
	</div>
<?php endif ?>

<?php
	$this->displayPoll();
	$this->displayModulePosition( 'kunena_poll' );
	$this->displayTopicActions();
?>

<div class="kblock">
	<div class="kheaderhei">
		<h1bai><span><?php echo JText::_('COM_KUNENA_TOPIC') ?> <?php echo $this->escape($this->topic->subject) ?></span></h1>
		<?php $this->displayModulePosition( 'kunena_topictitle' ); ?>
		<?php if ($this->usertopic->favorite) : ?><div class="kfavorite"></div><?php endif ?>
		<?php if (!empty($this->keywords)) : ?><div class="kkeywords"><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->escape($this->keywords)) ?></div><?php endif ?>
	</div>
	<div class="kcontainer">
		<div class="kbody"><?php $this->displayMessages() ?></div>
	</div>
</div>




<div class="kblock">
	<div class="kcontainer">
<div class="kbody"><script type="text/javascript" id="wumiiRelatedItems"></script></div>
	</div>
</div>








<?php $this->displayTopicActions(); ?>




<div class="kcontainer klist-bottom">
	<div class="kbody">
		<div class="kmoderatorslist-jump fltrt">
				<?php $this->displayForumJump (); ?>
		</div>
		<?php if (!empty ( $this->moderators ) ) : ?>
		<div class="klist-moderators">
				<?php
				echo '' . JText::_('COM_KUNENA_MODERATORS') . ": ";
				$modlinks = array();
				foreach ( $this->moderators as $moderator) {
					$modlinks[] = $moderator->getLink ();
				}
				echo implode(', ', $modlinks);
				?>
		</div>
		<?php endif; ?>
	</div>
</div>





<script type="text/javascript">
    var wumiiSitePrefix = "http://shu.ai/";
    var wumiiParams = "&num=5&mode=2&pf=JAVASCRIPT";
</script>
<script type="text/javascript" src="http://widget.wumii.cn/ext/relatedItemsWidget"></script>

<script type="text/javascript">
    var wumiiSitePrefix = "http://ayo.net.au/";
    var wumiiParams = "&num=5&mode=2&pf=JAVASCRIPT";
</script>
<script type="text/javascript" src="http://widget.wumii.cn/ext/relatedItemsWidget"></script>












