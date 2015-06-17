<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kpbox">
	<div class="kcontainer" id="kprofilebox">
		<div class="kbody">
<table class="kprofilebox">
	<tbody>
		<tr class="krow1">
			<td valign="top" class="kprofileboxcnt">
				
				<?php if ($this->login->enabled()) : ?>
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="login">
					<input type="hidden" name="view" value="user" />
					<input type="hidden" name="task" value="login" />
					[K=TOKEN]

					<div class="input">
						<span>
							<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>
							<input type="text" name="username" class="inputbox ks" alt="username" size="12" />
						</span>
						<span>
							&nbsp; &nbsp; <?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD'); ?>
							<input type="password" name="password" class="inputbox ks" size="12" alt="password" /></span>
						<span>&nbsp; &nbsp; 
							<?php if($this->remember) : ?>
							<?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME'); ?>
							<input type="checkbox" name="remember" alt="" value="1" />
							<?php endif; ?>&nbsp; &nbsp;&nbsp; &nbsp;
							<input type="submit"  name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
						</span>



<span>&nbsp; &nbsp; </span><span class="kprofilebox-pass">
							<a href="<?php echo $this->lostPasswordUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD') ?></a>
						</span>

<span>&nbsp; &nbsp; </span><span class="kprofilebox-pass">
							<a href="register" ><?php echo JText::_('COM_KUNENA_REG') ?></a>
						</span>



					</div>
				


				</form>
				<?php endif; ?>
			</td>
			<!-- Module position -->
			<?php if ($this->moduleHtml) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php echo $this->moduleHtml; ?>
				</div>
			</td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>
