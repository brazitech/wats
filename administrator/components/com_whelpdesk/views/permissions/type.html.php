<?php
/**
 * @version $Id$
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
        $this->setLayout('type');

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
        WToolbarHelper::back('permissions.menu');
        WToolbarHelper::divider();
        WToolbarHelper::link($this->getModel('returnURI'), 'Cancel', 'cancel');
        WToolbarHelper::divider();
        WToolbarHelper::help('permissions-type');
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('SELECT TYPE'));
        WDocumentHelper::description(JText::sprintf('SELECT USERS OR USER GROUPS TO MODIFIY PERMISSIONS TO %s %s', $this->getModel('targetType'), $this->getModel('targetIdentifierAlias')));

        WDocumentHelper::addPathwayItem(JText::_('PERMISSIONS'), '', 'javascript: submitform(\'permissions.menu\')');
        WDocumentHelper::addPathwayItem(JText::_('TYPE'));
    }

}

?>
