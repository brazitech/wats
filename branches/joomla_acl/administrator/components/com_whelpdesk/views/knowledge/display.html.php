<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class KnowledgeHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('display');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        $this->document();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        if ($this->getModel('canEditKnowledge')) {
            JToolBarHelper::editList('knowledge.edit.start');
        }
        JToolbarHelper::divider();
        JToolbarHelper::help('knowledge', true);
    }

    private function document() {
        WDocumentHelper::subtitle($this->getModel()->name);

        WDocumentHelper::addPathwayItem(JText::_('WHD_KD:DOMAINS'), '', JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomains.display.start'));
        WDocumentHelper::addPathwayItem($this->getModel('knowledgedomain')->name, '', JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomain.display.start&alias='.$this->getModel('knowledgedomain')->alias));
        WDocumentHelper::addPathwayItem($this->getModel()->name);
    }

}

?>
