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
        $this->setLayout('user');

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
        WToolbarHelper::back('permissions.edit.selectRequestNodeType');
        WToolbarHelper::next('permissions.edit.setPermissions');
        WToolbarHelper::divider();
        WToolbarHelper::link($this->getModel('returnURI'), 'Cancel', 'cancel');
        WToolbarHelper::divider();
        WToolbarHelper::help('permissions.user', true);
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('SELECT USERS'));
        WDocumentHelper::description(JText::sprintf('SELECT USERS TO MODIFIY PERMISSIONS TO %s %s', JText::_($this->getModel('targetType')), base64_decode(JRequest::getVar('targetIdentifierAlias', 'UNKNOWN', 'REQUEST', 'BASE64'))));

        WDocumentHelper::addPathwayItem(JText::_('PERMISSIONS'), '', 'javascript: submitform(\'permissions.menu\')');
        WDocumentHelper::addPathwayItem(JText::_('TYPE'), '', 'javascript: submitform(\'permissions.edit.selectRequestNodeType\')');
        WDocumentHelper::addPathwayItem(JText::_('SELECT'));
    }

}

?>
