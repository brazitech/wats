<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class FaqcategoriesHTMLWView extends WView {
	
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
        JToolbarHelper::help('knowledgedomain.list', true);
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('FAQ CATEGORIES'));

        // set the subtitle
        WDocumentHelper::subtitle(JText::_('FAQ CATEGORIES'));
    }
}

?>
