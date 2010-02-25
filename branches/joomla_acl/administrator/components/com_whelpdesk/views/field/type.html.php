<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class FieldHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('type');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar and add metadata to the document
        $this->toolbar();
        $this->document();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        $command = WFactory::getCommand();
        WToolbarHelper::next('field.create.define');
        WToolbarHelper::divider();
        WToolBarHelper::cancel('fields.list');
        WToolbarHelper::divider();
        WToolbarHelper::help('field-type');
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD'), null, 'index.php?option=com_whelpdesk&task=fields.list');

        // work with the current term...
        $field = $this->getModel();
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD:NEW FIELD'));
        WDocumentHelper::subtitle(JText::_('WHD_CD:NEW FIELD'));
    }

}

?>
