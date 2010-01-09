<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'generic'.DS.'tmpl'.DS.'form.php');
