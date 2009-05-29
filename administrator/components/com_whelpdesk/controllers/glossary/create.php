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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

class GlossaryCreateWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('create');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD GLOSSARY CREATE ACCESS DENIED');
            return;
        }

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

        // get the dataset definition for this model
        wimport('database.fieldset');
        $dataset = WFieldset::getInstance('glossary');
        $dataset->setData($table);

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('glossary', 'form', $format);

        // add the default model to the view
        $view->addModel('term', $table, true);

        // add the dataset to the model
        $view->addModel('dataset', $dataset);
        $view->addModel('dataset-data', $table);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        return parent::commit($post);
    }
}

?>