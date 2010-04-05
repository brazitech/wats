<?php
/**
 * @version $Id: list.html.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class RequestHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('selectrequestcategory');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // add metadata to the document
        $this->document();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        $command = WFactory::getCommand();
        WToolbarHelper::cancel('request.list.start');
        WToolbarHelper::divider();
        WToolbarHelper::help('request-list');
    }

    private function document() {
        WDocumentHelper::subtitle(JText::_('WHD_R:SELECT HELP REQUEST CATEGORY'));
    }

}
