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
        $this->setLayout('form');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

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
        JToolBarHelper::save('glossary.'.WFactory::getCommand()->getUsecase().'.save');
        JToolBarHelper::apply('glossary.'.WFactory::getCommand()->getUsecase().'.apply');
        JToolBarHelper::cancel('glossary.list');
        JToolbarHelper::divider();
        JToolbarHelper::help('glossary.form', true);
    }

}

?>
