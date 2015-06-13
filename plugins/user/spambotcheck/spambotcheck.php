<?php

/**
 * @version		$Id: plgUserSpamCheck.php
 * @package		User SpamCheck - check for possible spambots during register and login
 * @author		vi-solutions, Robert Kuster
 * @copyright	Copyright (C) 2010 vi-solutions. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'spambotcheck' . DIRECTORY_SEPARATOR . 'SpambotCheck' . DIRECTORY_SEPARATOR . 'SpambotCheckImpl.php');
require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'spambotcheck' . DIRECTORY_SEPARATOR . 'SpambotCheck' . DIRECTORY_SEPARATOR . 'SpambotCheckHelpers.php');

class plgUserSpambotCheck extends JPlugin {

    //public function plgUserSpambotCheck(& $subject, $config) {
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        //load the translation
        $this->loadLanguage();
    }

    /**
     * Method is called before user data is stored in the database
     *
     * @param 	array		holds the old user data (without new changes applied)
     * @param 	boolean		true if a new user is stored
     */
    public function onUserBeforeSave($userOld, $isnew) {
        // only site and not administrator
        // only a new user
        if ( !JFactory::getApplication()->isSite() || !$isnew) {
            return true;
        }
        
        $this->params->set('current_action', 'Register');
        
        $data = JRequest::getVar('jform');
        $user = array(
            "fullname" => $data['name'],
            "username" => $data['username'],
            "email" => $data['email1']
        );
        $spamString = "";
        
        if ( !$this->isSpammer($user, $spamString)) {
            // not a spammer
            return true;
        }
        
        //check if users have lately registered with this IP
        if ($this->params->get('isSpamIp', 0) == 1) {
            $componentInstalled = plgSpambotCheckHelpers::checkComponentInstalled();
            if ($componentInstalled) {
                plgSpambotCheckHelpers::flagUserWithSpamUserIp();
            }
        }
        
        // send email notification to all sys-admins
        $this->sendMailToAdmin($user, $spamString, JText::_('PLG_USER_SPAMBOTCHECK_EMAIL_SUBJECT_REGISTER_PREVENTION_TXT'));
        
        // redirect us to the old page and display an error notificaton to the user	
        $usersConfig = JComponentHelper::getParams('com_users');
        $message = JText::_('PLG_USER_SPAMBOTCHECK_USER_REGISTRATION_SPAM_TXT');
        JLog::add($message, JLog::ERROR, 'jerror');
        $app = JFactory::getApplication();
        $app->redirect('index.php');
        $app->close();
        
        return false;
    }

    function onUserAfterSave($data, $isNew, $result, $error) {
        $userId = JArrayHelper::getValue($data, 'id', 0, 'int');
        //only for new users that were saved successfully in database
        if ($userId && $isNew && $result) {
            $componentInstalled = plgSpambotCheckHelpers::checkComponentInstalled();
            if ($componentInstalled) {
                //insert the new user into users_spambot table
                if (!plgSpambotCheckHelpers::logUserData($userId)) {
                    //No message
                    return false;
                }
                if (JFactory::getApplication()->isSite()) {
                    //check if a user has allready registered using the same IP
                    plgSpambotCheckHelpers::checkIpSuspicious($data, $this->params);
                    //check for suspicious email addresses
                    plgSpambotCheckHelpers::checkEmailSuspicious($data);
                }
            }
        }

        return true;
    }

    function onUserAfterDelete($user, $success, $msg) {
        $userId = JArrayHelper::getValue($user, 'id', 0, 'int');
        if ($userId && $success) {
            $componentInstalled = plgSpambotCheckHelpers::checkComponentInstalled();
            if ($componentInstalled) {
                // get Ip of deleted user
                $userIp = plgSpambotCheckHelpers::getTableFieldValue('#__user_spambotcheck', 'ip', 'user_id', $userId);
                //Delete row in table user_spambotcheck
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('user_id') . ' = ' . $db->quote($userId)
                );

                $query->delete($db->quoteName('#__user_spambotcheck'));
                $query->where($conditions);

                $db->setQuery($query);
                $result = $db->execute();

                //clean up user_spambotcheck fields
                plgSpambotCheckHelpers::cleanUserSpambotTable($userIp, $userId);
            }
        }
        
        return true;
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @access	public
     * @param   array   holds the user data
     * @param 	array   array holding options (remember, autoregister, group)
     * @return	boolean	True on success
     */
    public function onUserLogin($user, $options = array()) {
        // don't monitor log-ins 
        if (!($this->params->get('spbot_monitor_events', 'RL') == 'RL')) {
            return true;
        }

        //Is user trusted and not to check?
        $userId = plgSpambotCheckHelpers::getTableFieldValue('#__users', 'id', 'email', $user['email']);
        $componentInstalled = plgSpambotCheckHelpers::checkComponentInstalled();
        if ($componentInstalled && plgSpambotCheckHelpers::getTableFieldValue('#__user_spambotcheck', 'trust', 'user_id', $userId) == 1) {
            return true;
        }
        
        $this->params->set('current_action', 'Login');
        $spamString = "";
        
        // not a spammer ?
        if ( !$this->isSpammer($user, $spamString)) {
            return true;
        }

        // this is a spammer
        if (($spamString != "") && (strpos($spamString, 'E-Mail in Backlist') === false)) {
            //set user to suspicious if not allready done
            //get Value of note field
            if ($componentInstalled) {
                $notevalue = plgSpambotCheckHelpers::getTableFieldValue('#__user_spambotcheck', 'note', 'user_id', $userId);
                if (strpos($notevalue, '4: User flagged; ') === false) {
                    $note = '4: User flagged; ';
                    // Create an object for the record we are going to update.
                    $object = new stdClass();
                    $object->user_id = $userId;

                    //Add a note
                    $object->note = $notevalue . $note;
                    //Set suspicious state

                    $object->suspicious = 0;

                    // Update their details in the users table using user_id as the primary key.
                    $result = JFactory::getDbo()->updateObject('#__user_spambotcheck', $object, 'user_id');
                }

                //check if users have lately registered with this IP
                if ($this->params->get('isSpamIp', 0) == 1) {
                    plgSpambotCheckHelpers::flagUserWithSpamUserIp($userId);
                }
            }
        }

        // User is allready logged in by task done in plgUserJoomla::onUserLogin
        // Enforce a logout operation by resetting fields in session table to a guest user

        $config = JComponentHelper::getParams('com_users');
        $defaultUserGroup = $config->get('new_usertype', 2);

        // create a guest user		 
        $instance = new JUser(); // creates a guest user	
        $instance->set('id', 0);
        $instance->set('name', '');
        $instance->set('username', '');
        $instance->set('groups', array($defaultUserGroup));

        // get the session
        $session = JFactory::getSession();

        // replace session user with guest user
        $session->set('user', $instance);

        // -> store the guest user to the #__session table using the session id of the session created by the spammer
        // thus replacing the logged in spammer with a guest user
        $table = JTable::getInstance('session'); // Get the session-table object
        $table->load($session->getId());
        $table->guest = $instance->get('guest');
        $table->username = $instance->get('username');
        $table->userid = intval($instance->get('id'));
        $table->set('groups');
        $table->update();

        // send email notification to all sys-admins
        if (($spamString != "") && (strpos($spamString, 'E-Mail in Backlist') === false)) {
            $this->sendMailToAdmin($user, $spamString, JText::_('PLG_USER_SPAMBOTCHECK_EMAIL_SUBJECT_LOGIN_PREVENTION_TXT'));
        }

        // redirect us to the old page and display an error notificaton to the user
        JLog::add(sprintf(JText::_('PLG_USER_SPAMBOTCHECK_USER_LOGIN_SPAM_TXT')), JLog::ERROR, 'jerror');
        $app = JFactory::getApplication();
        $app->redirect(JRoute::_($options['return']));
        $app->close();

        return false;
    }

    /**
     * Method check if the user specified is a spammer.
     *
     * @param 	array		holds the user data
     * @param 	string		hold the raw string returned by "check_spammers_plain.php" 
     * 
     * @return boolean True if user is a spammer and False if he isn't. 
     */
    function isSpammer($user, &$spamString) {
        // don't check admins
        if (plgSpambotCheckHelpers::userIsAdmin($user)) {
            return false;
        }

        // check for spammer
        $SpambotCheck = new plgSpambotCheckImpl($this->params, $user['email'], $_SERVER['REMOTE_ADDR'], $user['username']);
        $SpambotCheck->checkSpambots();
        if ($SpambotCheck->sIdentifierTag == false || strlen($SpambotCheck->sIdentifierTag) == 0 || strpos($SpambotCheck->sIdentifierTag, "SPAMBOT_TRUE") === false) {
            // not a spammer
            $spamString = "";
            return false;
        }
        
        // if we get here we have to deal with a spammer		
        $spamString = $SpambotCheck->sIdentifierTag;
        return true;
    }

    /**
     * Send an e-mail about the failed login/register attempt to all admins.
     *
     * @param 	array		holds the user data
     * @param 	string		hold the raw string returned by "check_spammers_plain.php"
     * @param 	string		string added to the e-mail subject 
     * 
     * @return boolean True if user is a spammer and False if he isn't. 
     */
    function sendMailToAdmin(&$user, &$spamString, $subjectAddString) {
        if (!$this->params->get('spbot_email_notifications', 1)) {
            // -> NO admin notifications
            return;
        }
        
        //get Super User Groups
        $superUserGroups = plgSpambotCheckHelpers::getSuperUserGroups();
        if (!(count($superUserGroups) > 0)) {
            // Something went wrong with finding superadmins, don't sent mails to everybody
            return;
        }
        
        // Only send notifications for selected types
        $type = $this->params->get('current_action');
        $notificationtype = $this->params->get('email_notification_type');

        if (($notificationtype == "RL") || ($notificationtype == "R" && $type == "Register") || ($notificationtype == "L" && $type == "Login")) {
            $name = $user['fullname'];
            $username = $user['username'];
            $email = $user['email'];
            $sPostersIP = $_SERVER['REMOTE_ADDR'];

            $app = JFactory::getApplication();
            $sitename = $app->getCfg('sitename');
            $mailfrom = $app->getCfg('mailfrom');
            $fromname = $app->getCfg('fromname');

            // get all super administrator
            // create where statement for SQL
            $where = "";
            $length = count($superUserGroups);
            if ($length > 0) {
                $where .= 'WHERE ';
                for ($i = 0; $i < $length; $i++) {
                    $where .= 'map.group_id = ' . $superUserGroups[$i];
                    if ($i < $length - 1) {
                        $where .= ' OR ';
                    }
                }
            }

            $db = JFactory::getDBO();
            $query = 'SELECT u.name AS name, u.email AS email, u.sendEmail AS sendEmail FROM `#__users` AS u LEFT JOIN `#__user_usergroup_map` AS map ON map.user_id = u.id LEFT JOIN `#__usergroups` AS g ON map.group_id = g.id ' . $where;

            $db->setQuery($query);
            $rows = $db->loadObjectList();

            // Send notification to all administrators
            $subject = sprintf(JText::_('PLG_USER_SPAMBOTCHECK_ACCOUNT_DETAILS_FOR_TXT'), $name, $sitename) . $subjectAddString;
            $subject = html_entity_decode($subject, ENT_QUOTES);

            foreach ($rows as $row) {
                if ($row->sendEmail) {
                    $message = sprintf(JText::_('PLG_USER_SPAMBOTCHECK_SEND_EMAIL_TO_ADMIN_TXT'), $row->name, $sitename, $type, $name, $email, $username, $sPostersIP, $spamString);
                    $message = html_entity_decode($message, ENT_QUOTES);
                    $mailer = JFactory::getMailer();
                    // Clean the email data
                    $subject = JMailHelper::cleanSubject($subject);
                    $message = JMailHelper::cleanBody($message);
                    $mailer->sendMail($mailfrom, $fromname, $row->email, $subject, $message);
                }
            }
        } 
        else {
            //No Admin Notification
            return;
        }
    }

}
