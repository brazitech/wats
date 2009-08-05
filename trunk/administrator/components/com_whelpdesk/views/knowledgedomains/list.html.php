<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class KnowledgedomainsHTMLWView extends WView {
	
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
            WToolbarHelper::addNew('knowledgedomains.create.start');
        }
        WToolbarHelper::editList('knowledgedomain.edit.start');
        WToolbarHelper::deleteList('knowledgedomain.delete.start');
        WToolbarHelper::divider();
        WToolBarHelper::publishList('knowledgedomain.state.publish');
        WToolBarHelper::unpublishList('knowledgedomain.state.unpublish');
        WToolbarHelper::divider();
        WToolBarHelper::display('knowledgedomains.display.start');
        WToolbarHelper::divider();
        WToolbarHelper::permissions();
        WToolbarHelper::divider();
        WToolbarHelper::help('knowledgedomain.list', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_KD:DOMAINS'));

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_KD:DOMAINS'));
    }
}

?>
