<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package wats
 */

// Don't allow direct linking
defined('_JEXEC') or die('Restricted Access');

class menuWATS
{

	function WATS_LIST()
	{
		JToolbarHelper::addNew();
	}
	
	function WATS_EDIT()
	{
		JToolbarHelper::apply();
		JToolbarHelper::cancel();
	}
	
	function WATS_EDIT_BACKUP()
	{
		JToolbarHelper::apply();
		JToolbarHelper::cancel();
		JToolbarHelper::spacer();
		// $task='', $icon='', $iconOver='', $alt='', $listSelect=true
		JToolbarHelper::custom('backup', 'export', 'download_f2.png', $alt='Backup', false);
		
		$document =& JFactory::getDocument();
		$document->addStyleDeclaration(".icon-32-export { background-image: url(templates/khepri//images/toolbar/icon-32-export.png); }");

	}
	
	function WATS_NEW()
	{
		JToolbarHelper::save();
		JToolbarHelper::cancel();
	}
	
}
?>