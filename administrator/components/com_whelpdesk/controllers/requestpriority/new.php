<?php
/**
 * @version $Id: edit.php 105 2009-05-04 12:46:26Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

class RequestPriorityNewWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setUsecase('new');
        $this->setType('requestpriority');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('requestpriority');

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
               WMessageHelper::message(JText::sprintf('WHD_RP:CREATED REQUEST PRIORITY %s', JRequest::getString('name')));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'requestpriority.list.start');
                   $model->checkIn($id);
               } else {
                   JRequest::setVar('task', 'requestpriority.edit.start');
                   JRequest::setVar('id',   $id);
               }
               return;
            }
        }

        // get the view
        $view = WView::getInstance('requestpriority', 'form',
                                strtolower(JFactory::getDocument()->getType()));

        // add the default model to the view
        $view->addModel('form', $form, true);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($model) {
        // values to use to create new record
        $post = JRequest::get('POST');

        // commit the changes
        return parent::commit(0, $post, $model);
    }
}
