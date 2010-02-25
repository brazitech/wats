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
        $this->setLayout('setpermissions');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // add metadata to the document
        $this->document();

        if ($this->getModel('targetType') == 'helpdesk' && $this->getModel('targetIdentifier') == 'helpdesk') {
            JError::raiseNotice(0, 'YOU ARE ATTEMPTING TO MODIFY THE DEFAULT PERMISSIONS, MODIFYING THESE PERMISSIONS CAN MAKE IT DIFFICULT TO TRACK ACCESS RULES');
        }

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        WToolbarHelper::back('permissions.edit.findUserRequestNode');
        WToolbarHelper::divider();
        WToolbarHelper::save('permissions.edit.savePermissions');
        WToolbarHelper::apply('permissions.edit.savePermissions');
        WToolbarHelper::link($this->getModel('returnURI'), 'Cancel', 'cancel');
        WToolbarHelper::divider();
        WToolbarHelper::help('permissions-setpermissions');
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('PERMISSIONS'));
        WDocumentHelper::description(JText::sprintf('MODIFIY PERMISSIONS TO %s %s', JText::_($this->getModel('targetType')), $this->getModel('targetIdentifierAlias')));

        WDocumentHelper::addPathwayItem(JText::_('PERMISSIONS'), '', 'javascript: submitform(\'permissions.menu\')');
        WDocumentHelper::addPathwayItem(JText::_('TYPE'), '', 'javascript: submitform(\'permissions.edit.selectRequestNodeType\')');
        WDocumentHelper::addPathwayItem(JText::_('SELECT'), '', 'javascript: submitform(\'permissions.edit.findUserRequestNode\')');

        // add the current item to the end of the pathway
        WDocumentHelper::addPathwayItem(JText::_('SET PERMISSIONS'));
    }

}

?>
