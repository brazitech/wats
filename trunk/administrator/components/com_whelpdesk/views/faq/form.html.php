<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class FaqHTMLWView extends WView {

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
            WToolBarHelper::cancel($command->getType().'.'.WFactory::getCommand()->getUsecase().'.cancel');
        } else {
            WToolBarHelper::cancel('faq.list');
        }
        WToolbarHelper::divider();
        WToolbarHelper::help('faq.form', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('FAQ'), null, 'index.php?option=com_whelpdesk&task=faqcategories.list');

        // work with the current term...
        $faq = $this->getModel();
        if ($faq->id) {
            WDocumentHelper::addPathwayItem($faq->question);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('EDITING FAQ %s', $faq->question));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('NEW FAQ'));
            WDocumentHelper::subtitle(JText::_('NEW FAQ'));
        }
    }

}

?>
