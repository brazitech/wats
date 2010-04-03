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
        $this->setLayout('display');

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
        if ($this->getModel('canList')) {
            WToolBarHelper::showList('glossary.list.start');
        }
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_GLOSSARY'));

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_GLOSSARY'));
    }

}

?>
