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

class RequestCategoryNewWController extends RequestCategoryWController {

    public function  __construct() {
        //parent::__construct();
        $this->setUsecase('new');
        $this->setType('requestcategory');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('requestcategory');

        // get the JForm
        $form = $model->getForm(null, true);

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply')
        {
            // before saving or applying the KD, make sure the token is valid
            shouldHaveToken();

            // attempt to save
            $id = $this->commit($model);
            if ($id !== false) {
               // successfully saved changes
               WMessageHelper::message(JText::sprintf('WHD_RC:CREATED REQUEST CATEGORY %s', JRequest::getString('name')));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'requestcategory.list.start');
                   $model->checkIn($id);
               } else {
                   JRequest::setVar('task', 'requestcategory.edit.start');
                   JRequest::setVar('id',   $id);
               }
               return;
            }
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

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($model) {
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
        return parent::commit(0, $post, $model);
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