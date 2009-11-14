<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class KnowledgedomainsHTMLWView extends WView {
	
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

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        if ($this->getModel('canList')) {
            WToolBarHelper::showList('knowledgedomains.list.start');
        }
    }
}

?>
