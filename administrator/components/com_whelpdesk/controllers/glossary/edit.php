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

class GlossaryEditWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('edit');
    }

    /**
     * @todo check token
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD GLOSSARY EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY UNKNOWN TERM'));
            return;
        }
        $table->load($id);

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // attempt to save
            if ($this->commit()) {
               JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY SAVED'));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'glossary.list.start');
               } else {
                   JRequest::setVar('task', 'glossary.edit.start');
               }

               return;
            } else {
                JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                foreach($table->getErrors() AS $error) {
                    JError::raiseNotice('INPUT', $error);
                }
            }
        }

        // check if we should show the state buttons
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;

        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        
        // check if we should show the reset hits button
        $canResetHits = false;

        try {
            $canResetHits = $accessSession->hasAccess('user', $user->get('id'),
                                                      'glossary', 'glossary',
                                                      'glossary', 'resethits');
        } catch (Exception $e) {
            $canResetHits = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('glossary', 'form', $format);

        // add the default model to the view
        $view->addModel('term', $table, true);

        // add the dataset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the boolean value describing access to reset hits
        $view->addModel('canResetHits', $canResetHits);
        $view->addModel('canChangeState', $canChangeState);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        // check if we should allow state change
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        if (!$canChangeState) {
            unset($post['published']);
        }

        // commit the changes
        return parent::commit($post);
    }
}

?>