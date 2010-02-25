<?php
/**
 * @version $Id: list.html.php 126 2009-06-11 10:13:55Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class PermissionsHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('menu');

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
        WToolbarHelper::link($this->getModel('returnURI'), 'Cancel', 'cancel');
        WToolbarHelper::divider();
        WToolbarHelper::help('permissions-type');
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('PERMISSIONS'));
        WDocumentHelper::description(JText::sprintf('WHAT DO YOU WANT TO DO WITH %s %s?', $this->getModel('targetType'), $this->getModel('targetIdentifierAlias')));

        WDocumentHelper::addPathwayItem(JText::_('PERMISSIONS'));
    }

}

?>
