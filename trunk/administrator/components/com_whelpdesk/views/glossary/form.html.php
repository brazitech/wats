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
        JToolBarHelper::save('glossary.'.WFactory::getCommand()->getUsecase().'.save');
        JToolBarHelper::apply('glossary.'.WFactory::getCommand()->getUsecase().'.apply');
        JToolBarHelper::cancel('glossary.list');
        JToolbarHelper::divider();
        JToolbarHelper::help('glossary.form', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('GLOSSARY'), null, 'index.php?option=com_whelpdesk&task=glossary.list');

        // work with the current term...
        $term = $this->getModel();
        if ($term->id) {
            WDocumentHelper::addPathwayItem($term->term);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('EDITING GLOSSARY TERM %s', $term->term));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('NEW GLOSSARY ITEM'));
            WDocumentHelper::subtitle(JText::_('NEW GLOSSARY ITEM'));
        }
    }

}

?>
