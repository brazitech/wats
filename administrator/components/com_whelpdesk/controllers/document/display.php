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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'document.php');

/**
 * 
 */
class DocumentDisplayWController extends DocumentWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT DISPLAY ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('document');
        $table->load($this->getAccessTargetIdentifier());

        // get the model
        $model = WModel::getInstanceByName('document');

        // check if we can download the document
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canDownload = false;
        try {
            $canDownload = $accessSession->hasAccess('user', $user->get('id'),
                                                   'document', $table->id,
                                                   'document', 'download');
        } catch (Exception $e) {
            $canDownload = false;
        }

        // get the parents
        $parents = $model->getParents($table->parent);

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('document', 'display', $format);

        // add the default model, the document
        $view->addModel('document', $table, true);

        // add the creator to the view
        $view->addModel('creator', JFactory::getUser($table->created_by));

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the parents to the view
        $view->addModel('parents', $parents);

        // add the boolean values describing access
        $view->addModel('canDownload', $canDownload);

        // display the view!
        JRequest::setVar('view', 'display');
        $this->display();
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return WModel::getId();
    }
}

?>