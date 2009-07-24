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

/**
 * import the parent class GlossaryWController
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * Controller that enables users to create new glossary terms
 */
class GlossaryCreateWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('create');
    }

    /**
     * @todo
     * @throws WInvalidTokenException
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD_GLOSSARY:CREATE ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('glossary');

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // before saving or applying the new term, make sure the token is valid
            shouldHaveToken();

            // attempt to save
            $id = $this->commit();
            if ($id !== false) {
               WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:SAVED TERM %s', JRequest::getString('term')));
               if ($stage == 'save') {
                   // return to the list
                   JRequest::setVar('task', 'glossary.list.start');
               } else {
                   // goto the edit page
                   JRequest::setVar('task', 'glossary.edit.start');
                   JRequest::setVar('id',   $id);
               }
               // no need to continue we will now be going to the list or edit page
               return;
            } else {
                // form input is not valid - output errors
                JError::raiseWarning('500', JText::_('WHD_FORM:INVALID'));;
                foreach($table->getErrors() AS $error) {
                    JError::raiseWarning('500', $error);
                }
            }
        }

        // get the view
        $view = WView::getInstance(
            'glossary',
            'form',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model to the view
        $view->addModel('term', $table, true);

        // add the custom fields to the view
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    /**
     * Overrides GlossaryWController::commit() and automatically pulls in the
     * new data from the request (this must be POST data).
     *
     * @return bool|int On fail returns boolean false, on success returns the PK value
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        return parent::commit($post);
    }
}

?>