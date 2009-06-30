<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class DocumentcontainerHTMLWView extends WView {

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
        
        if ($this->getModel('canCreate')) {
            WToolBarHelper::addNew('documentcontainer.create', 'New Folder');
        }
        if ($this->getModel('canCreateDocument')) {
            WToolBarHelper::addNew('document.upload', 'Upload File');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::edit('documentcontainer.edit.start');
        }
        if ($this->getModel()->id != 1 && $this->getModel('canDelete')) {
            WToolbarHelper::delete('documentcontainer.delete.start');
        }
        WToolbarHelper::divider();
        if ($this->getModel()->id != 1 && $this->getModel('canMove')) {
            WToolbarHelper::move('documentcontainer.move.start');
        }
        if ($this->getModel()->id != 1 && $this->getModel('canMove')) {
            WToolbarHelper::here('documentcontainer.move.here');
        }
        WToolbarHelper::divider();
        WToolbarHelper::permissions();
        WToolbarHelper::divider();
        WToolbarHelper::help('documents.display', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle($this->getModel()->name);

        // populate the pathway
        $parents = $this->getModel('parents');
        for ($i = 0, $c = count($parents) ; $i < $c ; $i++) {
            WDocumentHelper::addPathwayItem($parents[$i]->name,
                                            $parents[$i]->description,
                                            'index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$parents[$i]->id);
        }

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem($this->getModel()->name);
    }

}

?>
