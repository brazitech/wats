<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class GlossaryHTMLWView extends WView {

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
            WToolbarHelper::addNew('glossary.create.start');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::editList('glossary.edit.start');
        }
        if ($this->getModel('canDelete')) {
            WToolbarHelper::deleteList('WHD_GLOSSARY:DELETE?', 'glossary.delete.start');
        }
        if ($this->getModel('canChangeState')) {
            WToolbarHelper::divider();
            WToolBarHelper::publishList('glossary.state.publish');
            WToolBarHelper::unpublishList('glossary.state.unpublish');
        }
        WToolbarHelper::divider();
        WToolBarHelper::display('glossary.display.start');
        WToolbarHelper::divider();
        WToolbarHelper::permissions();
        WToolbarHelper::divider();
        WToolbarHelper::help('glossary.list', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_GLOSSARY'));

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_GLOSSARY'));
    }

}

?>
