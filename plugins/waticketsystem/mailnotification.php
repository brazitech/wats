<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgWaticketsystemMailnotification extends JPlugin {

	function plgWaticketsystemMailnotification(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	function onUpdate() {
		return true;
	}
}