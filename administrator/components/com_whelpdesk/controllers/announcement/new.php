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

class AnnouncementNewWController extends WController {

    public function  __construct() {
        //parent::__construct();
        $this->setDefaultView('new');
        $this->setType('announcement');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the model and form
        $model = WModel::getInstanceByName('announcement');
        $form = $model->getForm(null, true, 'new');

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply')
        {
            // before saving or applying the announcement, make sure the token is valid
            shouldHaveToken();

            // attempt to save
            $id = $this->commit($model);
            if ($id !== false) {
               // successfully saved changes
               WMessageHelper::message(JText::sprintf('WHD_A:CREATED ANNOUNCEMENT %s', JRequest::getString('name')));
               if ($stage == 'save') {
                   JRequest::setVar('task', 'announcement.list.start');
                   $model->checkIn($id);
               } else {
                   JRequest::setVar('task', 'announcement.edit.start');
                   JRequest::setVar('id',   $id);
               }
               return;
            }
        }

        // get the view
        $view = WView::getInstance('announcement', 'form',
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
        unset($post['id']);

        // set the user by whom the request was created
        $post['created_by'] = JFactory::getUser()->get('id');

        // commit the changes
        return parent::commit(0, $post, $model);
    }

}
