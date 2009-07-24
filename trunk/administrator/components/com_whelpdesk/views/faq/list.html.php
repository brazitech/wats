<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class FaqHTMLWView extends WView {
	
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
            WToolbarHelper::addNew('faq.create.start');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::editList('faq.edit.start');
        }
        if ($this->getModel('canDelete')) {
            WToolbarHelper::deleteList(JText::_('ARE YOU SURE YOU WANT TO DELETE THE SELCTED FAQs?'), 'faq.delete.start');
        }
        if ($this->getModel('canChangeState')) {
            WToolbarHelper::divider();
            WToolBarHelper::publishList('faq.state.publish');
            WToolBarHelper::unpublishList('faq.state.unpublish');
        }
        WToolbarHelper::divider();
        WToolbarHelper::help('faq.list', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_FAQ:FAQs'));

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_FAQ:FAQs'));
    }
}

?>
