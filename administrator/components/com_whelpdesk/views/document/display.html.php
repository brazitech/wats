<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class DocumentHTMLWView extends WView {

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
        JToolBarHelper::save('document.'.WFactory::getCommand()->getUsecase().'.save');
        JToolBarHelper::apply('document.'.WFactory::getCommand()->getUsecase().'.apply');
        JToolBarHelper::cancel('documentcontainer.display');
        JToolbarHelper::divider();
        JToolbarHelper::help('documentcontainer.form', true);
    }

    private function document() {
        // populate the pathway
        $parents = $this->getModel('parents');
        for ($i = 0, $c = count($parents) ; $i < $c ; $i++) {
            WDocumentHelper::addPathwayItem($parents[$i]->name,
                                            $parents[$i]->description,
                                            'index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$parents[$i]->id);
        }

        // add description
        WDocumentHelper::description(JText::_('TO MODIFY THIS DOCUMENT USE THE CONTEXT BUTTON ON THE MAIN PAGE'));
        // work with the current document...
        $document = $this->getModel();
        WDocumentHelper::subtitle($document->name);
        WDocumentHelper::addPathwayItem($document->name);
    }

}

?>
