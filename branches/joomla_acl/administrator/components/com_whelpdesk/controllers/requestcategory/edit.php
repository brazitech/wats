<?php
/**
 * @version $Id: edit.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'requestcategory.php');

class RequestCategoryEditWController extends RequestCategoryWController {

    public function  __construct() {
        //parent::__construct();
        $this->setUsecase('edit');
        $this->setType('requestcategory');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the dientifier
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'requestcategories.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD_RC:UNKNOWN REQUEST CATEGORY'));
            return;
        }

        // get the data
        $model = WModel::getInstanceByName('requestcategory');
        $requestCategory = $model->getRequestCategory($id);

        // make sure the data loaded
        if(!$requestCategory) {
            JRequest::setVar('task', 'requestcategories.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_RC:UNKNOWN REQUEST CATEGORY'));
            return;
        }

        // make sure the RC isn't already checked out
        if ($requestCategory->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_RC:REQUEST CATEGORY ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_rc:REQUEST CATEGORY ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'requestcategories.list.start');
            return;
        }

        // get the JForm
        $form = $model->getForm($requestCategory, true);

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'requestcategories.list.start');
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
                   WMessageHelper::message(JText::sprintf('WHD_RC:UPDATED REQUEST CATEGORY %s', JRequest::getString('name')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'requestcategories.list.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'requestcategory.edit.start');
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
        $view = WView::getInstance('requestcategory', 'form', 
                                strtolower(JFactory::getDocument()->getType()));

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

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'knowledgedomain.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN UNKNOWN'));
        }
        return $id;
    }
}

?>