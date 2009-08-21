<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

class FieldsHTMLWView extends WView {
	
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
            WToolbarHelper::addNew('field.create.start');
        }
        if ($this->getModel('canEdit')) {
            WToolbarHelper::editList('field.edit.start');
        }
        if ($this->getModel('canDelete')) {
            WToolbarHelper::deleteList(JText::_('ARE YOU SURE YOU WANT TO DELETE THE SELCTED FIELDS?'), 'field.delete.start');
        }
        WToolbarHelper::divider();
        WToolbarHelper::custom(
            'datagroups.list',
            'datagroups',
            'datagroups',
            'WHD_CD:GROUPS',
            false,
            false
        );
        WToolbarHelper::divider();
        WToolbarHelper::help('fields.list', true);

        JFactory::getDocument()->addStyleDeclaration(
            '.icon-32-datagroups {background-image:url(components/com_whelpdesk/assets/icons/view_multicolumn.png);}'
        );
    }

    private function document() {
        // set the subtitle
        WDocumentHelper::subtitle(JText::_('WHD_CD:CUSTOM FIELDS'));

        $filters = $this->getModel('filters');
        $filteringOnGroup = false;
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD'));
        foreach ($filters['groups'] as $group) {
            if (@$group->filtering) {
                foreach ($filters['tables'] as $table) {
                    if (@$table->id == $group->table) {
                        WDocumentHelper::addPathwayItem(JText::_($table->name));
                        continue;
                    }
                }
                WDocumentHelper::addPathwayItem($group->label);
                $filteringOnGroup = true;
                continue;
            }
        }
        if (!$filteringOnGroup) {
            foreach ($filters['tables'] as $table) {
                if (@$table->filtering) {
                    WDocumentHelper::addPathwayItem(JText::_($table->name));
                }
            }
        }
        WDocumentHelper::addPathwayItem(JText::_('WHD_CD:CUSTOM FIELDS'));
    }
}

?>
