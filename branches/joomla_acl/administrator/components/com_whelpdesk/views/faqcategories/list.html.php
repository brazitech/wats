<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class FaqcategoriesHTMLWView extends WView {
	
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

        // add metadata to the document
        $this->document();

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
            WToolbarHelper::addNew('faqcategories.create.start');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::editList('faqcategory.edit.start');
        }
        if ($this->getModel('canDelete')) {
            WToolbarHelper::deleteList(JText::_('ARE YOU SURE YOU WANT TO DELETE THE SELCTED FAQ CATEGORIES?'), 'faqcategory.delete.start');
        }
        WToolbarHelper::divider();
        WToolbarHelper::display('faqcategories.display.start');
        WToolbarHelper::divider();
        WToolBarHelper::permissions();
        WToolbarHelper::divider();
        WToolbarHelper::help('faqcategory.list', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_FAQ:CATEGORIES'));

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_FAQ:CATEGORIES'));
    }
}

?>
