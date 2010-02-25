<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class DatagroupsHTMLWView extends WView {
	
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
            WToolbarHelper::addNew('datagroup.create.start');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::editList('datagroup.edit.start');
        }
        if ($this->getModel('canDelete')) {
            WToolbarHelper::deleteList(JText::_('ARE YOU SURE YOU WANT TO DELETE THE SELECTED DATAGROUPS?'), 'datagroup.delete.start');
        }
        WToolbarHelper::divider();
        WToolbarHelper::custom(
            'fields.list',
            'fields',
            'fields',
            'WHD_CD:FIELDS',
            false,
            false
        );
        WToolbarHelper::divider();
        WToolbarHelper::help('fields-list');

        JFactory::getDocument()->addStyleDeclaration(
            '.icon-32-fields {background-image:url(components/com_whelpdesk/assets/icons/xclipboard-32.png);}'
        );
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_CD:GROUPS'));

        $filters = $this->getModel('filters');
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD'));
        foreach ($filters['tables'] as $table) {
            if (@$table->filtering) {
                WDocumentHelper::addPathwayItem(JText::_($table->name));
            }
        }
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD:GROUPS'));
    }
}

?>
