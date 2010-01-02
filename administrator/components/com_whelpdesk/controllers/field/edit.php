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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'field.php');

class FieldEditWController extends FieldWController {

    public function  __construct() {
        $this->setType('field');
        $this->setUsecase('edit');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get CK
        $record = WModel::getId(true);
        $idSeparatorPosition = stripos($record, '.');
        $group = intval(substr($record, 0, $idSeparatorPosition));
        $name  = preg_replace('(^a-z\_)', '', substr($record, $idSeparatorPosition + 1));

        // get the data
        $model = WModel::getInstanceByName('field');
        $field = $model->getField($group, $name);

        // make sure the data loaded
        if(!$field) {
            JRequest::setVar('task', 'fields.list.start');
            JError::raiseWarning('INPUT', JText::sprintf('WHD_CD:UNKNOWN FIELD %s.%s', $group, $name));
            return;
        }

        // make sure the field isn't already checked out
        /*if ($field->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_CD:FIELD ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_CD:FIELD ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'fields.list.start');
            return;
        }*/

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($group, $name);
                JRequest::setVar('task', 'fields.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the term, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $record = $this->commit($group, $name);
                if ($record) {
                   // successfully saved changes
                   WMessageHelper::message(JText::sprintf('WHD_CD:UPDATED FIELD %s', JRequest::getString('label')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'fields.list.start');
                       /*$model->checkIn($group, $name);*/
                   } else {
                       JRequest::setVar('task', 'field.edit.start');
                       JRequest::setVar('group',  $record->group);
                       JRequest::setVar('name',   $record->name);
                   }
                   return;
                }
        }

        // get the view
        $view = WView::getInstance(
            'field',
            'form',
            strtolower(JFactory::getDocument()->getType())
        );

        // add the default model to the view
        $view->addModel('field', $field, true);

        // check out the record
        //$model->checkOut($group, $name);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($group, $name) {
        // values to use to create new record
        $post = JRequest::get('POST');

        unset($post['group']);
        unset($post['name']);

        // commit the changes
        return parent::commit($group, $name, $post);
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'faqcategory.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD FAQ UNKNOWN'));
        }
        return $id;
    }
}

?>