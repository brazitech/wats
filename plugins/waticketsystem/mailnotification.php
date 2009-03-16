<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

define("WPATH_PLUGINS", JPATH_PLUGINS . DS . "waticketsystem");
define("WPATH_PLUGIN_MAILNOTIFICATION_TEMPLATES", WPATH_PLUGINS . DS . "mailnotification");

class plgWaticketsystemMailnotification extends JPlugin {

    var $_mergeDataObject = null;
    
	function plgWaticketsystemMailnotification(&$subject, $config) {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	function onTicketNew(&$ticket) {
        $this->_sendNotification($ticket, "new");
	}
    
    function onTicketReply(&$ticket) {
		$this->_sendNotification($ticket, "reply");
	}
    
    function onTicketReopen(&$ticket) {
		$this->_sendNotification($ticket, "reopen");
	}
    
    function _sendNotification(&$ticket, $type) {
        // check if we are supposed to notifying anyone
        if ($this->params->get($type . "-enabled", 0) != 1) {
            return;
        }
        
        // get the mailer object
        $mailer = JFactory::getMailer();
        
        // prepare the message
        $message =& $ticket->_msgList[count($ticket->_msgList) - 1];
        
        // get the template 
        jimport("joomla.filesystem.file");
        $templateFormat = ($this->params->get("email-format") == "text") ? "text" : "html";
        $templateName = $this->params->get($type . "-tmpl-" . $this->params->get("email-format", "html"), $type);
        $templatePath = WPATH_PLUGIN_MAILNOTIFICATION_TEMPLATES . DS . $templateFormat . DS . $templateName . ".tmpl";
        $template = $this->_prepareTemplate($templatePath, $ticket, $message);
        
        // get the email addresses of those users who we want to notify
        $users =& $this->_getRelatedUsers($ticket);
        
        // loop through users
        for ($i = 0; $i < count($users); $i++) {
            // add recipient
            $user =& $users[$i];
            $mailer->addRecipient($user->email);
            
            // add other recipients if this is the user that initiaited the event
            if ($user->watsid == $message->watsId) {
                $others = $this->params->get("email-others", "");
                if (strlen($others)) {
                    $others = preg_split("~\s*(\,\;)\s*~", $others);
                    for ($n = 0; $n < count($others); $n++) {
                        $mailer->addBCC($others[$n]);
                    }
                }
            }
            
            // set content
            $mailer->setSubject(JText::sprintf($this->params->get($type."-subject"), $ticket->name));
            $mailer->IsHTML(($templateFormat == "html"));
            $emailBody = $this->_mergeData($template, "~\{user\.([a-z]+)\}~i", $user);
            $mailer->setBody($emailBody);
            
            // send email
            $result = $mailer->Send();
            if ($result !== true) {
                // uh oh, sending of email failed!
                JError::raiseWarning("500", JText::sprintf("FAILED TO SEND NOTIFICATION TO %s", $user->username));
            }
        }
    }
    
    function _prepareTemplate($templatePath, &$ticket, &$message) {
        // read the template file
        $template = JFile::read($templatePath);
        
        // translate template
        $template = preg_replace_callback(
            "~\{\?(.+)\?\}~", 
            array($this, "_prepareTemplateTranslateCallback"),
            $template
        );
        
        // prepare ticket safe data
        $safeTicket = new stdclass();
        $safeTicket->watsId = $ticket->watsId;
        $safeTicket->username = $ticket->username;
        $safeTicket->ticketId = $ticket->ticketId;
        $safeTicket->name = $ticket->name;
        $safeTicket->category;
        $safeTicket->lifeCycle;
        $safeTicketDatetime = JFactory::getDate(strtotime($ticket->datetime));
        $safeTicket->datetime = $safeTicketDatetime->toFormat();
        $safeTicket->msgNumberOf = $ticket->msgNumberOf;
        $safeTicket->uri = JRoute::_("index.php?option=com_waticketsystem&act=ticket&task=view&ticketid=" . $ticket->ticketId);
        
        // prepare message safe data
        $safeMessage = new stdclass();
        $safeMessage->msgId = $message->msgId;
        $safeMessage->msg = $message->msg;
        $safeMessage->watsId = $message->watsId;
        $safeMessageUser = JFactory::getUser($safeMessage->watsId);
        $safeMessage->username = $safeMessageUser->get("username");
        $safeMessageDatetime = JFactory::getDate(strtotime($message->datetime));    
        $safeMessage->datetime = $safeMessageDatetime->toFormat();
        
        // site
        $site = new stdclass();
        $site->uri = JRoute::_("index.php");
        
        // merge the ticket and message safe data with the template
        $template = $this->_mergeData($template, "~\{ticket\.([a-z]+)\}~i", $safeTicket);
        $template = $this->_mergeData($template, "~\{message\.([a-z]+)\}~i", $safeMessage);
        $template = $this->_mergeData($template, "~\{site\.([a-z]+)\}~i", $site);
        
        // return the template
        return $template;
    }
    
    function _prepareTemplateTranslateCallback($matches) {
        return JText::_($matches[1]);
    }
    
    function _mergeData($string, $pattern, &$object) {
        
        // temporarily assign the object for callback purposes
        $this->_mergeDataObject =& $object;
        
        // merge the data
        $string = preg_replace_callback(
            $pattern, 
            array($this, "_mergeDataCallback"),
            $string
        );
        
        // unset object we're all done!
        unset($this->_mergeDataObject);
        
        return $string;
    }
    
    function _mergeDataCallback($matches) {
        $property = $matches[1];
        return (is_string($this->_mergeDataObject->$property)) ? $this->_mergeDataObject->$property : $property;
    }
    
    function &_getRelatedUsers(&$ticket) {
        // get the DBO
        $database =& JFactory::getDBO();
        
        // build and execute query to get users who are related to the ticket
        $query = "SELECT DISTINCT m.watsid, u.email, u.username, u.name FROM #__wats_msg AS m LEFT  JOIN #__users AS u ON m.watsid=u.id WHERE m.ticketid = " . intval($ticket->ticketId) . " AND u.block = 0";
        $database->setQuery($query);
        $users = $database->loadObjectList();
        
        // return the users
        return $users;
    }
}