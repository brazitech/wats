<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class FaqcategoryHTMLWView extends WView {

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
        if ($this->getModel()->id) {
            WToolBarHelper::cancel($command->getType().'.'.$command->getUsecase().'.cancel');
        } else {
            WToolBarHelper::cancel('faqcategories.list');
        }
        WToolbarHelper::divider();
        WToolbarHelper::help('faqcategory.form', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_FAQ:CATEGORIES'), null, 'index.php?option=com_whelpdesk&task=faqcategories.list');

        // work with the current term...
        $category = $this->getModel();
        if ($category->id) {
            WDocumentHelper::addPathwayItem($category->name);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('WHD_FAQ:EDIT FAQ CATEGORY %s', $category->name));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('WHD_FAQ:NEW FAQ CATEGORY'));
            WDocumentHelper::subtitle(JText::_('WHD_FAQ:NEW FAQ CATEGORY'));
        }
    }

}

?>
