<?php
/**
 * @version $Id: create.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

/**
 * Get parent class
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomains.php');

class KnowledgedomainsCreateWController extends KnowledgedomainsWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('create');
    }

    /**
     * Create a new knowledge domain. Valid stages are "start", "save" and 
     * "apply". Note that "save" and "apply" are essentially the same except 
     * that "apply" redirects to the edit page, whereas "save" redirects to the 
     * list page.
     *
     * @param string @stage Stage at which the usecase is to be executed
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAINS CREATE ACCESS DENIED');
            return;
        }

        // get the model and the data
        $model = WModel::getInstanceByName('knowledgedomain');
        $table = $model->getKnowledgeDomain(0);

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // attempt to save
            if ($this->commit()) {
               JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN SAVED'));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'knowledgedomains.list.start');
               } else {
                   JRequest::setVar('task', 'knowledgedomain.edit.start');
               }
               
               return;
            } else {
                JError::raiseNotice('INPUT', JText::_('WHD_FORM:INVALID'));;
                foreach($table->getErrors() AS $error) {
                    JError::raiseNotice('INPUT', $error);
                }
            }
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('knowledgedomains', 'form', $format);

        // add the default model to the view
        $view->addModel('knowledgedomain', $table, true);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    /**
     * Commits a new knowledge domain to the database. This method extracts data
     * from the POST request. Note that it also removes ID from the data so as
     * to ensure we don't update an existing KD instead of creating a new one.
     *
     * @return boolean
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        return parent::commit(0, $post);
    }
}

?>