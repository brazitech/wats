<?php
/**
 * @version $Id: list.php 122 2009-05-29 14:49:37Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'documentcontainer.php');

/**
 * 
 */
class DocumentcontainerCreateWController extends DocumentcontainerWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('create');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT CONTAINER CREATE ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('documentcontainer');
        $table->parent = $this->getAccessTargetIdentifier();

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // attempt to save
            if ($this->commit()) {
               JError::raiseNotice('INPUT', JText::_('WHD DOCUMENT CONTAINER SAVED'));
               if ($stage == 'save') {
                   JRequest::setVar('id',   JRequest::getInt('parent', 1));
                   JRequest::setVar('task', 'documentcontainer.display.start');
               } else {
                   JRequest::setVar('task', 'documentcontainer.edit.start');
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

        // get the model
        $model = WModel::getInstance('documentcontainer');

        // get the parents
        $parents = $model->getPath($table->parent);

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('documentcontainer', 'form', $format);

        $view->addModel('container', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the parents to the view
        $view->addModel('parents', $parents);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        // make sure parent is an integer
        $post['parent'] = intval($post['parent']);

        return parent::commit($post);
    }

    /**
     *
     * @return int
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return JRequest::getInt('parent', 1);
    }
}

?>