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
        WToolBarHelper::save($command->getType().'.'.$command->getUsecase().'.save');
        WToolBarHelper::apply($command->getType().'.'.$command->getUsecase().'.apply');
        if ($this->getModel()->name) {
            WToolBarHelper::cancel($command->getType().'.'.WFactory::getCommand()->getUsecase().'.cancel');
        } else {
            WToolBarHelper::cancel('fields.list');
        }
        WToolbarHelper::divider();
        WToolbarHelper::help('field.form', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD'), null, 'index.php?option=com_whelpdesk&task=fields.list');

        // work with the current term...
        $field = $this->getModel();
        WDocumentHelper::addPathwayItem(JText::_($field->tableName));
        WDocumentHelper::addPathwayItem($field->groupLabel);
        if ($field->version != null) {
            WDocumentHelper::addPathwayItem($field->label);
            WDocumentHelper::subtitle(JText::sprintf('WHD_CD:EDITING FIELD %s', $field->label));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('WHD_CD:NEW FIELD'));
            WDocumentHelper::subtitle(JText::_('WHD_CD:NEW FIELD'));
        }
    }

}

?>
