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

        // get the filters
        $this->filter();

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

    private function filter() {
        // get everything we need
        $application = JFactory::getApplication();

        // prepare the filter lists
        $lists = array();

        // search filter
        $lists["search"] = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.search",
                                                                 "search",
                                                                 "",
                                                                 "string");
        $lists["search"]  = JString::strtolower($lists["search"]);

        // publishing filter
        $lists["state"] = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.state",
                                                                "filter_state",
                                                                "",
                                                                "word");
        $lists["state"] = JHTML::_("grid.state", $lists["state"]);

        // ordering
        $lists["order"] = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.order",
                                                                "filter_order",
                                                                "term",
                                                                "cmd");

        // ordering direction
        $lists["orderDirection"] = $application->getUserStateFromRequest("com_whelpdesk.glossary.filter.orderDirection",
                                                                         "filter_order_Dir",
                                                                         "ASC",
                                                                         "cmd");
        $lists["orderDirection"] = strtoupper($lists["orderDirection"]) == 'ASC' ? 'ASC' : 'DESC';

        // add the lists
        $this->addModel("lists", $lists);
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
