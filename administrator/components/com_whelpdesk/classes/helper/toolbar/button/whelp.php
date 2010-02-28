<?php
/**
 * @version		$Id: help.php 14577 2010-02-04 07:12:36Z eddieajau $
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

// get base class
require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'toolbar'.DS.'button'.DS.'help.php');

class JButtonWhelp extends JButtonHelp
{
	protected $_name = 'Whelp';

	protected function _getCommand($ref, $com)
	{
        $url = sprintf("http://groups.google.com/group/webamoeba-helpdesk/web/%s?hl=%s", $ref, "en-GB");
		$cmd = "popupWindow('$url', '".JText::_('Help', true)."', 850, 480, 1)";

		return $cmd;
	}
}
