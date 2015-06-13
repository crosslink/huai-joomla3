<?php

/**
 * @version		$Id: script.php 22354 2011-11-07 05:01:16Z github_bot $
 * @package		com_visforms
 * @subpackage	plg_visforms_spamcheck
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class plguserspambotcheckInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsable for running this script
	 */
	public function __construct(JAdapterInstance $adapter)
	{
		// installing component manifest file version
		$this->release = $adapter->get( "manifest" )->version; 
		// manifest file minimum Joomla version
		$this->minimum_joomla_release = $adapter->get( "manifest" )->attributes()->version;
		// installed version
		$this->oldRelease = "";
		
		// holds data for user messages
		$this->status = new stdClass();
		$this->status->plugins = array();
		$this->status->modules = array();
		$this->status->components = array();
		$this->status->tables = array();
		$this->status->folders = array();
	}
   
	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter)
	{
		$jversion = new JVersion();

		// abort if the current Joomla release is older
		if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt'))
		{
			Jerror::raiseWarning(null, JText::_('PLG_USER_SPAMBOTCHECK_WRONG_JOOMLA_VERSION') .$this->minimum_joomla_release);
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($route == 'update')
		{
			$this->oldRelease = $this->getManifestParameter('version');
			$rel = $this->oldRelease . JText::_('PLG_USER_SPAMBOTCHECK_TO') . $this->release;
			if ( version_compare( $this->release, $this->oldRelease, 'le' ) ) {
				JLog::add(JText::_('PLG_USER_SPAMBOTCHECK_WRONG_VERSION') . $rel, JLog::ERROR, 'jerror');
				return false;
			}
		}
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	//public function postflight($route, JAdapterInstance $adapter);
	public function postflight($route, JAdapterInstance $adapter)
	{
		// run version specific update code
		if($route == 'update')
		{
			if(version_compare($this->oldRelease, '1.3.11', 'lt'))
				$this->postFlightForVersion_1_3_11();
			if(version_compare($this->oldRelease, '1.3.12', 'lt'))
				$this->postFlightForVersion_1_3_12();
			if(version_compare($this->oldRelease, '1.3.13', 'lt'))
				$this->postFlightForVersion_1_3_13();
		}
		
		// enable plugin
		$db = JFactory::getDbo();
		$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote('spambotcheck')." AND folder=".$db->Quote('user');
		$db->setQuery($query);
		$db->execute();
		
		// install included component
		$this->installComponent($route, $adapter);
	}

	/**
	 * install the included components
	 *
	 */
	private function installComponent($route, JAdapterInstance $adapter)
	{
		// install Component
		$db = JFactory::getDbo();
		
		$src = $adapter->getParent()->getPath('source');
		$manifest = $adapter->getParent()->manifest;
		$components = $manifest->xpath('components/component');
		
		foreach ($components as $component)
		{
			$element = (string)$component->attributes()->element;
			
			//check if component is allready installed
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND element = ".$db->Quote($element)."";
			$db->setQuery($query);
			$extensions = $db->loadColumn();
		
			$path = $src.'/components/'.$element;
			$installer = new JInstaller;
			if (count($extensions))
			{
				$result = $installer->update($path);
			}
			else
			{
				$result = $installer->install($path);
			}
			
			// prepare display text
			$name = JText::_((string)$component->attributes()->name);
			$desc = JText::_((string)$component->attributes()->desc);
			$feature = JText::_((string)$component->attributes()->feature);
			$info = JText::_((string)$component->attributes()->info);
			$message = '';
			$this->status->components[] = array('result' => $result, 'name' => $name, 'desc' => $desc, 'feature' => $feature, 'info' => $info, 'message' => $message);
			
			// show all results on final install page
			$this->installationResults($route, $this->status);
		}		
	}
	
	private function postFlightForVersion_1_3_11()
	{		
		JLog::add('*** Perform postflight for Version 1.3.11 ***', JLog::INFO, 'plgspambotcheck');
		// nothing to do for this version
	}
	
	private function postFlightForVersion_1_3_12()
	{		
		JLog::add('*** Perform postflight for Version 1.3.12 ***', JLog::INFO, 'plgspambotcheck');
		
		// inspect value of parameter spbot_monitor_events: value format changed from 0/1 to R/RL
		$name = 'spbot_monitor_events';
		$params = $this->getExtensionParameters();
		if(array_key_exists($name, $params))
		{
			if($params[$name] === '0')
				$params[$name] = 'R';
			if($params[$name] === '1')
				$params[$name] = 'RL';
		}
		
		// add new parameters
		$params['spbot_blacklist_email'] = '';
		$params['spbot_bl_log_to_db'] = '0';
		$params['spbot_suspicious_time']= '12';
		$params['spbot_allowed_hits'] = '3';
			
		$this->setExtensionParameters($params);
	}
	
	private function postFlightForVersion_1_3_13()
	{		
		JLog::add('*** Perform postflight for Version 1.3.13 ***', JLog::INFO, 'plgspambotcheck');
	
		// get all extension parameters
		$params = $this->getExtensionParameters();
		
		// remove spambusted.com related parameter
		$name = 'spbot_spambusted';
		if(array_key_exists($name, $params))
		{
			unset($params[$name]);
		}
		
		// remove deprecated spacer parameter (used to structure the parameter ui layout)
		$name = '@spacer';
		if(array_key_exists($name, $params))
		{
			unset($params[$name]);
		}
		
		// add new parameters
		$name = 'spbot_projecthoneypot_api_key';
		$param = $this->getExtensionParameter($name);
		$value = is_string($param) && $param != '' ? '1' : '0';
		$params['spbot_projecthoneypot'] = $value;
		
		// set all parameters
		$this->setExtensionParameters($params);
	}
	
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter)
	{
		// give a warning if cURL is not enabled on system; plugin will not be able to identify spammer
		$extension = 'curl';
		if (!extension_loaded($extension)) {
			JLog::add(JText::_('PLG_USER_SPAMBOTCHECK_CURL_MISSING'), JLog::WARNING, 'jerror');
		}
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter)
	{
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();

		if ($db) 
		{
			$tnfull = $db->getPrefix(). '_spambot_attempts';
			$tablesAllowed = $db->getTableList(); 	

			if (!in_array($tnfull, $tablesAllowed))
			{
				$db->setQuery("drop table if exists #__spambot_attempts");
				try
				{
					$result = $db->execute();
				}
				catch (Exception $e) 
				{
					JLog::add('Could not delete database table #__spambot_attempts', JLog::WARNING, 'jerror');
					return false;
				}
			}
		}
		
		$manifest = $adapter->getParent()->manifest;
		$components = $manifest->xpath('components/component');
		foreach ($components as $component)
		{
			$element = (string)$component->attributes()->element;
			$db = JFactory::getDBO();
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND element = ".$db->Quote($element)."";
			$db->setQuery($query);
			$extensions = $db->loadColumn();
			if (count($extensions))
			{
				foreach ($extensions as $id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('component', $id);
				}
				
				// prepare display text
				$name = JText::_((string)$component->attributes()->name);
				
				$this->status->components[] = array('result' => $result, 'name' => $name);
			}
		}
		$this->uninstallationResults($this->status);
	}
	
	/*
	 * gets all parameters of extension from extension table
	 */
	function getExtensionParameter($name) {
		// read the existing component value(s)
		$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "User - SpambotCheck"');
		$params = json_decode( $db->loadResult(), true );
		return $params[$name];
	}
	
	/*
	 * gets all parameters of extension from extension table
	 */
	function getExtensionParameters() {
		// read the existing component value(s)
		$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "User - SpambotCheck"');
		$params = json_decode( $db->loadResult(), true );
		return $params;
	}
	
	/*
	 * sets all parameters as extension parameters to extension table
	 */
	function setExtensionParameters($params) {
		// write the existing component value(s)
		$db = JFactory::getDbo();
		$paramsString = json_encode($params);
		$db->setQuery('UPDATE #__extensions SET params = ' . $db->quote( $paramsString ) . ' WHERE name = "User - SpambotCheck"' );
		try
		{
			$result = $db->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getManifestParameter($name)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "User - SpambotCheck"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[$name];
	}
	
	private function installationResults($route, $status)
	{
		$language = JFactory::getLanguage();
		$language->load('plg_spambotcheck');
		$rows = 0;
		$version = ($route == 'install')
				? JText::_('PLG_USER_SPAMBOTCHECK_INSTALL_VERSION') . $this->release
				: JText::_('PLG_USER_SPAMBOTCHECK_UPDATE_VERSION') .  $this->oldRelease . JText::_('PLG_USER_SPAMBOTCHECK_TO') . $this->release;
		// info: install info or new feature text for update
		$info = ($route == 'install')
				? JText::_('PLG_USER_SPAMBOTCHECK_INSTALL_INFO')
				: JText::_('PLG_USER_SPAMBOTCHECK_NEW_FEATURE');
	?>

	<div class="span12" style="font-weight:normal">
		<p><strong><?php echo $version; ?></strong></p>
		<p><?php echo JText::_('PLG_USER_SPAMBOTCHECK_INSTALL_MESSAGE'); ?></p>
		<img src="<?php echo JURI::base(); ?>/components/com_spambotcheck/images/logo-banner.png" alt="" align="right" />
		<h2><?php echo JText::_('PLG_USER_SPAMBOTCHECK_INSTALLATION_STATUS'); ?></h2>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title" style="text-align: left;"><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_PLUGIN'); ?></th>
					<th width="40%" style="text-align: left;"><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_STATUS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0">
					<td><p><strong><?php echo JText::_('PLG_USER_SPAMBOTCHECK_PLUGIN_NAME'); ?></strong></p>
					<p><?php echo JText::_('PLG_USER_SPAMBOTCHECK_DESC'); ?></p>
					</td>
					<td><p><strong><?php echo JText::_('PLG_USER_SPAMBOTCHECK_INSTALLED'); ?></strong></p>
					<p><?php echo $info; ?></p>
					</td>
				</tr>
				<?php if (count($status->components)): ?>
				<tr>
					<th><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_COMPONENT'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->components as $component):
					// no empty strings
					is_string($component['name']) && $component['name'] != "" ? $name = $component['name'] : $name = "";
					is_string($component['desc']) && $component['desc'] != "" ? $desc = $component['desc'] : $desc = "";
					is_string($component['feature']) && $component['feature'] != "" ? $feature = $component['feature'] : $feature = "";
					is_string($component['info']) && $component['info'] != "" ? $info = $component['info'] : $info = "";
					is_string($component['message']) && $component['message'] != "" ? $message = $component['message'] : $message = "";
					// success or error text
					if($component['result']) {
						$state = JText::_('PLG_USER_SPAMBOTCHECK_INSTALLED');
						$style = '';
						// info: install info or new feature text for update
						$info = ($route == 'install') ? $info : $feature;
					}
					else {
						$state = JText::_('PLG_USER_SPAMBOTCHECK_NOT_INSTALLED');
						// info: coloured error message
						$style = ' style="color: red"';
						// info: error message generated during coder execution
						$info = $message;
					}
				?>
				<tr class="row<?php echo(++$rows % 2); ?>">
					<td class="key"><p><strong><?php echo $name; ?></strong></p>
					<p><?php echo $desc; ?></p>
					</td>
					<td><p><strong<?php echo $style; ?>><?php echo $state; ?></strong></p>
					<p><?php echo $info; ?></p>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php
	}
	
	private function uninstallationResults($status)
	{
		$language = JFactory::getLanguage();
		$language->load('plg_spambotcheck');
		$rows = 0;
	 ?>
		
	<div class="span12" style="font-weight:normal">
		<h2><?php echo JText::_('PLG_USER_SPAMBOTCHECK_REMOVAL_STATUS'); ?></h2>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title" style="text-align: left;"><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_PLUGIN'); ?></th>
					<th style="text-align: left;"><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_STATUS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0">
					<td class="key"><?php echo JText::_('PLG_USER_SPAMBOTCHECK_PLUGIN_NAME'); ?></td>
					<td><strong><?php echo JText::_('PLG_USER_SPAMBOTCHECK_REMOVED'); ?></strong></td>
				</tr>
				<?php if (count($status->components)): ?>
				<tr>
					<th><?php echo JText::_('PLG_USER_SPAMBOTCHECK_HEADER_COMPONENT'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->components as $component):
					// no empty strings
					is_string($component['name']) && $component['name'] != "" ? $name = $component['name'] : $name = "";
					// success or error text
					if($component['result']) {
						$state = JText::_('PLG_USER_SPAMBOTCHECK_REMOVED');
						$style = '';
					}
					else {
						$state = JText::_('PLG_USER_SPAMBOTCHECK_NOT_REMOVED');
						// info: coloured error message
						$style = ' style="color: red"';
					}
				?>
				<tr class="row<?php echo(++$rows % 2); ?>">
					<td class="key"><?php echo $name; ?></td>
					<td><p><strong<?php echo $style; ?>><?php echo $state; ?></strong></p>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php
	}
}
?>
