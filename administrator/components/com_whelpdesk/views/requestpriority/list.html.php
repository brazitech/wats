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
        $this->setLayout('list');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // add metadata to the document
        $this->document();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        $command = WFactory::getCommand();
        WToolbarHelper::addNew('requestpriority.new.start');
        WToolbarHelper::edit('requestpriority.edit.start');
        WToolbarHelper::divider();
        WToolbarHelper::publishList('requestpriority.state.publish');
        WToolbarHelper::unpublishList('requestpriority.state.unpublish');
        WToolbarHelper::divider();
        WToolbarHelper::help('requestpriority-list');
    }

    private function document() {
        WDocumentHelper::subtitle(JText::_('WHD_RP:REQUEST PRIORITIES'));

        // add glossary to the pathway
        /*WDocumentHelper::addPathwayItem(JText::_('WHD_KD:DOMAIN'), null, 'index.php?option=com_whelpdesk&task=knowledgedomains.list');

        // work with the current term...
        $knowledgedomain = $this->getModel();
        if ($knowledgedomain->id) {
            WDocumentHelper::addPathwayItem($knowledgedomain->name);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('WHD_KD:EDITING KNOWLEDGE DOMAIN %s', $knowledgedomain->name));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('WHD_KD:NEW KNOWLEDGE DOMAIN'));
            WDocumentHelper::subtitle(JText::_('WHD_KD:NEW KNOWLEDGE DOMAIN'));
        }*/
    }

}

?>