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
        WToolBarHelper::saveAsCopy('glossary.copy.start');
        WToolBarHelper::save('glossary.'.WFactory::getCommand()->getUsecase().'.save');
        WToolBarHelper::apply('glossary.'.WFactory::getCommand()->getUsecase().'.apply');
        if ($this->getModel()->id) {
            WToolBarHelper::cancel('glossary.'.WFactory::getCommand()->getUsecase().'.cancel');
        } else {
            WToolBarHelper::cancel('glossary.list');
        }
        WToolbarHelper::divider();
        WToolbarHelper::help('glossary.form', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_GLOSSARY'), null, 'index.php?option=com_whelpdesk&task=glossary.list');

        // work with the current term...
        $term = $this->getModel();
        if ($term->id) {
            WDocumentHelper::addPathwayItem($term->term);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('WHD_GLOSSARY:EDITING TERM %s', $term->term));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('WHD_GLOSSARY:NEW GLOSSARY ITEM'));
            WDocumentHelper::subtitle(JText::_('WHD_GLOSSARY:NEW GLOSSARY ITEM'));
        }
    }

}

?>
