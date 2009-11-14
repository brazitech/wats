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
    
    public static function saveAndAddNew($task = 'save', $alt = 'JTOOLBAR_SAVE_AND_NEW') {
        JToolBar::getInstance('toolbar')->appendButton('Standard', 'new', $alt, $task, false, false);
    }

    public static function saveAsCopy($task = 'copy', $alt = 'JTOOLBAR_SAVE_AS_COPY') {
        JToolBar::getInstance('toolbar')->appendButton('Standard', 'copy', $alt, $task, false, false);
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

    private static $permissions = false;

    public static function permissions($task = 'permissions.menu', $alt = 'Permissions') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'permissions', $alt, $task, false, false);

        if (!self::$permissions) {
            // add CSS
            $style = ".icon-32-permissions { background-image: url(components/com_whelpdesk/assets/icons/32-lock.png); }";
            JFactory::getDocument()->addStyleDeclaration($style);
            self::$permissions = true;
        }
	}

    private static $move = false;

    public static function move($task = 'move', $alt = 'Move') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'move', $alt, $task, false, false);

        if (!self::$move) {
            // add CSS
            $style = ".icon-32-move { background-image: url(components/com_whelpdesk/assets/icons/32-windows_fullscreen.png); }";
            JFactory::getDocument()->addStyleDeclaration($style);
            self::$move = true;
        }
	}

    private static $here = false;

    public static function here($task = 'here', $alt = 'Here') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'here', $alt, $task, false, false);

        if (!self::$here) {
            // add CSS
            $style = ".icon-32-here { background-image: url(components/com_whelpdesk/assets/icons/32-windows_nofullscreen.png); }";
            JFactory::getDocument()->addStyleDeclaration($style);
            self::$here = true;
        }
	}

    public static function next($task = 'next', $alt = 'Next') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'forward', $alt, $task, false, false);
	}

    public static function back($task = 'back', $alt = 'Back') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'back', $alt, $task, false, false);
	}

    public static function link($url, $alt, $icon) {
		// Add a link button
		JToolBar::getInstance('toolbar')->appendButton('Link', $icon, $alt, $url);
    }

    public static function display($task = 'display', $alt = 'WHD:Display') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'display', $alt, $task, false, false);

        if (!self::$here) {
            // add CSS
            $style = ".icon-32-display { background-image: url(components/com_whelpdesk/assets/icons/imagegallery.png); }";
            JFactory::getDocument()->addStyleDeclaration($style);
            self::$here = true;
        }
    }

    public static function showList($task = 'list', $alt = 'WHD:List') {
		// Add a standard button
		JToolBar::getInstance('toolbar')->appendButton('Standard', 'display', $alt, $task, false, false);

        if (!self::$here) {
            // add CSS
            $style = ".icon-32-display { background-image: url(components/com_whelpdesk/assets/icons/view_text.png); }";
            JFactory::getDocument()->addStyleDeclaration($style);
            self::$here = true;
        }
    }

}
