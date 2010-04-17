<?php
/**
 * @version $Id: new.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.controller');
wimport('application.model');

class RequestNewWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('new');
        $this->setType('request');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model
        $model = WModel::getInstanceByName('request');
        $categoryId = JRequest::getInt('category_id');

        // Select a category first...
        if ($stage == 'start' || $categoryId < 1)
        {
            $this->selectRequestCategory();
            return;
        }

        // get the JForm
        $form = $model->getForm(null, true, 'new');
        $form->setValue('category_id', $categoryId, 'state');

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply')
        {
            // before saving or applying the KD, make sure the token is valid
            shouldHaveToken();

            // attempt to save
            $id = $this->commit($model);
            if ($id !== false) {
               // successfully saved changes
               WMessageHelper::message(JText::sprintf('WHD_R:CREATED HELP REQUEST %s', JRequest::getString('name')));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'request.list.start');
                   $model->checkIn($id);
               } else {
                   JRequest::setVar('task', 'request.edit.start');
                   JRequest::setVar('id',   $id);
               }
               return;
            }
        }

        // get the view
        $view = WView::getInstance('request', 'form', 
                                strtolower(JFactory::getDocument()->getType()));

        // add the default model to the view
        $view->addModel('form', $form, true);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    private function selectRequestCategory()
    {
        // get the category model
        $model = WModel::getInstanceByName('requestcategory');

        // get the view
        $this->setDefaultView('selectrequestcategory');
        $view = WView::getInstance(
            $this->getType(),
            'selectrequestcategory',
            strtolower(JFactory::getDocument()->getType())
        );

        // get the request categories data add to the view
        $rootRequestCategory = $model->getRootRequestCategory();
        $view->addModel('rootrequestcategory', $rootRequestCategory, true);

        // display the view!
        $this->display();
    }

    public function commit($model) {
        // values to use to create new record
        $post = JRequest::get('POST');

        // set the user by whom the request was created
        $post['created_by'] = JFactory::getUser()->get('id');

        // commit the changes
        return parent::commit(0, $post, $model);
    }

}
