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
        $this->setLayout('form');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar and document header
        $this->toolbar();
        $this->document();

        // add the editor
        $this->addModel('editor', JFactory::getEditor());

        // disable the menu
        JRequest::setVar('hidemainmenu', 1);

        if (!$this->getModel('knowledgeRevision')) {
            JError::raiseNotice('500', JText::_('WHD_KD:YOU ARE EDITING NEW KNOWLEDGE'));
        }

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
        WToolBarHelper::cancel($command->getType().'.'.$command->getUsecase().'.cancel');
        WToolbarHelper::divider();
        WToolbarHelper::help('knowledge.form', true);
    }

    private function document() {
        WDocumentHelper::subtitle(JText::sprintf('WHD_KD:EDITING KNOWLEDGE %s : %s', $this->getModel('knowledgeDomain')->name, $this->getModel()->name));

        WDocumentHelper::addPathwayItem(JText::_('WHD_KD:DOMAINS'), '', JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomains.display.start'));
        WDocumentHelper::addPathwayItem($this->getModel('knowledgeDomain')->name, '', JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomain.display.start&alias='.$this->getModel('knowledgeDomain')->alias));
        WDocumentHelper::addPathwayItem($this->getModel()->name);
    }

}

?>
