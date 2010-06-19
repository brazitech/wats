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

class RequestPriorityEditWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('edit');
        $this->setType('requestpriority');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the dientifier
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'requestpriority.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD_R:UNKNOWN REQUEST PRIORITY'));
            return;
        }

        // get the data
        $model = WModel::getInstanceByName('requestpriority');
        $requestPriority = $model->getRequestPriority($id);

        // make sure the data loaded
        if(!$requestPriority) {
            JRequest::setVar('task', 'requestpriority.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_R:UNKNOWN REQUEST PRIORITY'));
            return;
        }

        // make sure the RP isn't already checked out
        if ($requestPriority->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_R:REQUEST PRIORITY ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_R:REQUEST PRIORITY ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'requestpriority.list.start');
            return;
        }

        // get the JForm
        $form = $model->getForm($requestPriority, true, 'edit');

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'requestpriority.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the KD, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $id = $this->commit($id, $model);
                if ($id !== false) {
                   // successfullt saved changes
                   WMessageHelper::message(JText::sprintf('WHD_RP:UPDATED REQUEST PRIORITY %s',
                           JRequest::getString('name')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'requestpriority.list.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'requestpriority.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                }

                // bind new values to the form
                // $post = JRequest::get('POST');
                // $form->bind($post);
        }

        // check if we should show the state buttons
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;

        /*try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'knowledgedomain', WModel::getId(),
                                                        'knowledgedomain', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }*/

        // get the view
        $view = WView::getInstance('requestpriority', 'form', strtolower(JFactory::getDocument()->getType()));

        // add the default model to the view
        $view->addModel('form', $form, true);
        
        // add the boolean value describing access to change published state
        $view->addModel('canChangeState', $canChangeState);

        // check out the record
        $model->checkOut($id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($id, $model) {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        /*// check if we should allow state change
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'knowledgedomain', WModel::getId(),
                                                        'knowledgedomain', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        if (!$canChangeState) {
            unset($post['published']);
        }*/

        // commit the changes
        return parent::commit($id, $post, $model);
    }
}
