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
        JToolBarHelper::save('document.'.WFactory::getCommand()->getUsecase().'.save');
        JToolBarHelper::apply('document.'.WFactory::getCommand()->getUsecase().'.apply');
        JToolBarHelper::cancel('documentcontainer.display');
        JToolbarHelper::divider();
        JToolbarHelper::help('document.form', true);
    }

    private function document() {
        // populate the pathway
        $parents = $this->getModel('parents');
        for ($i = 0, $c = count($parents) ; $i < $c ; $i++) {
            WDocumentHelper::addPathwayItem($parents[$i]->name,
                                            $parents[$i]->description,
                                            'index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$parents[$i]->id);
        }

        // work with the current contaioner...
        $document = $this->getModel();
        if ($document->id) {
            WDocumentHelper::addPathwayItem($document->name);
            // set the subtitle
            WDocumentHelper::subtitle(JText::sprintf('EDITING DOCUMENT %s', $document->name));
        } else {
            WDocumentHelper::addPathwayItem(JText::_('UPLOAD DOCUMENT'));
            WDocumentHelper::subtitle(JText::_('UPLOAD DOCUMENT'));
        }
    }

}

?>
