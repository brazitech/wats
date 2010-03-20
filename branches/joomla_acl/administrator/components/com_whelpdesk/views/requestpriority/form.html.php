<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class RequestPriorityHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('form');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // add metadata to the document
        $this->document();

        // add the editor
        $this->addModel('editor', JFactory::getEditor());

        // disable the menu
        JRequest::setVar('hidemainmenu', 1);

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        $command = WFactory::getCommand();
        WToolBarHelper::save('requestpriority.'.$command->getUsecase().'.save');
        WToolBarHelper::apply('requestpriority.'.$command->getUsecase().'.apply');
        WToolBarHelper::cancel('requestpriority.list.start');
        WToolbarHelper::divider();
        WToolbarHelper::help('requestpriority-form');
    }

    private function document() {
        $requestCategory = $this->getModel();
        if ($requestCategory->getValue('id'))
        {
            WDocumentHelper::subtitle(JText::sprintf('WHD_RP:EDITING REQUEST PRIORITY %s', $requestCategory->getValue('name')));
        }
        else
        {
            WDocumentHelper::subtitle(JText::_('WHD_RP:NEW REQUEST PRIORITY'));
        }
    }

}
