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
            JError::raiseWarning('401', 'WHD_GLOSSARY:EDIT ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_GLOSSARY:UNKNOWN TERM'));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }
        if(!$table->load($id)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_GLOSSARY:UNKNOWN TERM'));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }

        // make sure the record isn't already checked out
        if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_GLOSSARY:TERM ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_GLOSSARY:TERM ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $table->checkIn();
                JRequest::setVar('task', 'glossary.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the term, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $id = $this->commit();
                if ($id !== false) {
                   // successfullt saved changes
                   WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:UPDATED TERM %s', JRequest::getString('term')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'glossary.list.start');
                       $table->checkIn();
                   } else {
                       JRequest::setVar('task', 'glossary.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                } else {
                    // An error occured, display the errors
                    JError::raiseNotice('500', JText::_('WHD_FORM:INVALID'));;
                    foreach($table->getErrors() AS $error) {
                        JError::raiseNotice('500', $error);
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
        $view = WView::getInstance(
            'glossary',
            'form',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model to the view
        $view->addModel('term', $table, true);

        // add the custom fields to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the boolean value describing access to reset hits
        $view->addModel('canResetHits', $canResetHits);
        $view->addModel('canChangeState', $canChangeState);

        // check out the record
        $table->checkOut(JFactory::getUser()->id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    /**
     * Overrides GlossaryWController::commit() and automatically pulls in the
     * new data from the request (this must be POST data). This takes account of
     * state change permissions such that the state cannot be changed through
     * this method unless the user has the necessary permissions.
     *
     * @return bool|int On fail returns boolean false, on success returns the PK value
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        // check if we should allow state change
        $canChangeState = false;
        try {
            $canChangeState = WFactory::getAccessSession()->hasAccess('user', JFactory::getUser()->get('id'),
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