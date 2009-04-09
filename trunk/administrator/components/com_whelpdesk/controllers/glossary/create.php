<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

class GlossaryCreateWController extends WController {

    public function __construct() {
        $this->setEntity('glossary');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the table
        $table = WFactory::getTable('glossary');

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // attempt to save
            if ($this->commit()) {
               JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY SAVED'));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'glossary.list.start');
               } else {
                   JRequest::setVar('task', 'glossary.edit.start');
                   // @todo set request id
               }
               
               return;
            } else {
                JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                foreach($table->getErrors() AS $error) {
                    JError::raiseNotice('INPUT', $error);
                }
            }
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('glossary', 'form', $format);

        // add the default model to the view
        $view->addModel('term', $table, true);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit() {
        // get the table
        $table = WFactory::getTable('glossary');

        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        $post['id'] = false;

        // allow raw untrimmed value for description
        $post['description'] = JRequest::getString('description', '', 'POST', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM);

        // bind $post with $table
        if (!$table->bind($post)) {
            // failed
            WFactory::getOut()->log('Failed to bind with table', true);
            return false;
        }

        // check the data is valid
        if (!$table->check()) {
            // failed
            WFactory::getOut()->log('Table data failed to check', true);
            return false;
        }

        // store the data in the database table and update nulls
        if (!$table->store(true)) {
            // failed
            WFactory::getOut()->log('Failed to save changes', true);
            return false;
        }

        WFactory::getOut()->log('Commited new glossary term to the database');
        return true;
    }
}

?>