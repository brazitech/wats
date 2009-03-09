<?php
/**
* FileName: toolbar.waticketsystem.html.php
* Date: 10/05/2006
* License: GNU General Public License
* Script Version #: 2.0.0
* JOS Version #: 1.0.x
* Development James Kennard jg8949@aol.com (www.webamoeba.co.uk)
**/

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