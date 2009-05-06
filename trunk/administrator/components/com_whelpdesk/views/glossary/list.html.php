<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class GlossaryHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('list');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // get the pagination
        $this->pagination();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        if ($this->getModel('canCreate')) {
            JToolbarHelper::addNew('glossary.create.start');
        }
        if ($this->getModel('canEdit')) {
            JToolbarHelper::editList('glossary.edit.start');
        }
        if ($this->getModel('canDelete')) {
            JToolbarHelper::deleteList('glossary.delete.start');
        }
        if ($this->getModel('canChangeState')) {
            JToolbarHelper::divider();
            JToolBarHelper::publishList('glossary.state.publish');
            JToolBarHelper::unpublishList('glossary.state.unpublish');
        }
        JToolbarHelper::divider();
        JToolbarHelper::help('glossary.list', true);
    }

    private function pagination() {
        // get the application object
        $app =& JFactory::getApplication();

        // get the limit and limitstart
        $limit = $app->getUserStateFromRequest("com_whelpdesk.glossary.list.limit", "limit", 0, "int");
        $limitstart = $app->getUserStateFromRequest("com_whelpdesk.glossary.list.limitstart", "limitstart", 0, "int");
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        // create JPagination object
        jimport('joomla.html.pagination');
        $this->addModel('pagination', new JPagination($this->getModel('total'), $limitstart, $limit));
    }

}

?>
