<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package wats
 */

// Don't allow direct linking
defined('_JEXEC') or die('Restricted Access');

require_once('toolbar.waticketsystem.html.php');



if ($act)
{
	switch ( $act )
	{
		case 'configure':
			menuWATS::WATS_EDIT();
			break;
		case 'ticket':
		case 'database':
		case 'about':
			// no menus
			break;
		case 'css':
			menuWATS::WATS_EDIT_BACKUP();
			break;
		default:
			switch ( $task )
			{
				case 'edit';
				case 'view';
					menuWATS::WATS_EDIT();
					break;
				case 'add';
					menuWATS::WATS_NEW();
					break;
				default:
					menuWATS::WATS_LIST();
					break;
			}
			break;
	}
}
?>