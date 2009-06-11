<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class HelpdeskHTMLWView extends WView {
	
    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('about');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // add metadata to the document
        $this->document();

        // continue!
        parent::render();
    }

    private function document() {
        WDocumentHelper::subtitle(JText::_('ABOUT'));
        WDocumentHelper::addPathwayItem(JText::_('ABOUT'));
    }
    
}

?>
