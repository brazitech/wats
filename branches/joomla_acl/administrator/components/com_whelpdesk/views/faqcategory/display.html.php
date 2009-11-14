<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class FaqcategoryHTMLWView extends WView {

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
        // add metadata to the document
        $this->document();

        // continue!
        parent::render();
    }

    private function document() {
        // add glossary to the pathway
        WDocumentHelper::addPathwayItem(JText::_('WHD_FAQ:CATEGORIES'), null, 'index.php?option=com_whelpdesk&task=faqcategories.display');

        // work with the current term...
        $category = $this->getModel();
        WDocumentHelper::addPathwayItem($category->name);
        // set the subtitle
        WDocumentHelper::subtitle($category->name);

        WDocumentHelper::description($category->description);
    }

}

?>
