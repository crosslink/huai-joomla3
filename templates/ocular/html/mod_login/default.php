<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

JHtml::_('behavior.keepalive');
?>
<?php if ($type == 'logout') : ?>
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
		<?php if ($params->get('greeting')) : ?>
			<div class="login-greeting">
				<?php if($params->get('name') == 0) : {
					echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
				} else : {
					echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
				} endif; ?>
			</div>
		<?php endif; ?>

		<div class="logout-button">
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.logout" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
<?php else : ?>
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
		<?php if ($params->get('pretext')): ?>
			<div class="pretext">
				<p><?php echo $params->get('pretext'); ?></p>
			</div>
		<?php endif; ?>
	 
		<fieldset class="userdata">
			<div class="ulogin1">
				<input id="modlgn-username" type="text" name="username" value="Username" class="inputbox"   />
			</div>

		 	<div class="ulogin2">
				<input id="modlgn-passwd" type="password" name="password" value="Password" class="inputbox"   />
			</div>

	    <div class="jlogin1">
				<!--<input type="submit" name="Submit" class="btnlogin" value="<?php //echo JText::_('JLOGIN') ?>" />-->
				<button class="btnlogin" type="submit"><?php echo JText::_('JLOGIN') ?></button> 
			</div>

			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
				<?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
	      <div class="jlogin2">
					<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
				</div>
			<?php endif; ?>
		
			<div style="clear:both;"></div>		
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	   <br>
		<div class="jlogintext">	
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
				<i class="fa fa-info-circle"></i>&nbsp;&nbsp;<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?>
			</a>
		<br>
	    <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
				<i class="fa fa-question-circle"></i>&nbsp;&nbsp;<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?>
			</a>
			<br>
			<?php
				$usersConfig = JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')) :
			?>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<i class="fa fa-user"></i>&nbsp;&nbsp;<?php echo JText::_('MOD_LOGIN_REGISTER'); ?>
				</a>
			<?php endif; ?>
		</div>
	
		<?php if ($params->get('posttext')): ?>
			<div class="posttext">
				<p>
					<?php echo $params->get('posttext'); ?>
				</p>
			</div>
		<?php endif; ?>
	</form>
<?php endif;