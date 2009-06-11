<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

/**
 * Admin toolbar helper, this class delegates most of the work to JToolbarHelper
 *
 * @todo
 */
abstract class WToolbarHelper implements WToolbarHelperInterface {

	public static function title($title, $icon = 'whelpdesk') {
		JToolbarHelper::title($title, $icon);
	}

	public static function divider() {
		JToolbarHelper::divider();
	}

	/**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	* @param boolean True if required to include callinh hideMainMenu()
	* @since 1.0
	*/
	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false)
	{
		$bar = & JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		$bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );
	}

	/**
	* Writes a custom option and task button for the button bar.
	* Extended version of custom() calling hideMainMenu() before submitbutton().
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	* @since 1.0
		* (NOTE this is being deprecated)
	*/
	public static function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$bar = & JToolBar::getInstance('toolbar');

		//strip extension
		$icon	= preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button
		$bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, true );
	}

	public static function help($ref, $com = false) {
		JToolBarHelper::help($ref, $com);
	}

	public static function addNew($task = 'add', $alt = 'New') {
		JToolBarHelper::addNew($task, $alt);
	}

	public static function publish($task = 'publish', $alt = 'Publish') {
		JToolBarHelper::publish($task, $alt);
	}

	public static function publishList($task = 'publish', $alt = 'Publish') {
		JToolBarHelper::publishList($task, $alt);
	}

	public static function unpublish($task = 'unpublish', $alt = 'Unpublish') {
		JToolBarHelper::unpublish($task, $alt);
	}

	public static function unpublishList($task = 'unpublish', $alt = 'Unpublish') {
		JToolBarHelper::unpublishList($task, $alt);
	}

	public static function editList($task = 'edit', $alt = 'Edit') {
		JToolBarHelper::editList($task, $alt);
	}

	public static function deleteList($msg = '', $task = 'remove', $alt = 'Delete') {
		JToolBarHelper::deleteList($msg, $task, $alt);
	}

	public static function deleteListX($msg = '', $task = 'remove', $alt = 'Delete') {
		JToolBarHelper::deleteListX($msg, $task, $alt);
	}

	public static function apply($task = 'apply', $alt = 'Apply') {
		JToolBarHelper::apply($task, $alt);
	}

	public static function save($task = 'save', $alt = 'Save') {
		JToolBarHelper::save($task, $alt);
	}

	public static function cancel($task = 'cancel', $alt = 'Cancel') {
		JToolBarHelper::cancel($task, $alt);
	}

    /**
     * Edit the current item
     */
    public static function edit($task = 'edit', $alt = 'Edit') {
		// Add an edit button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'edit', $alt, $task, false, false);
    }

    /**
     * Edit the current item
     */
    public static function delete($task = 'delete', $alt = 'Delete') {
		// Add an edit button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'delete', $alt, $task, false, false);
    }

}
