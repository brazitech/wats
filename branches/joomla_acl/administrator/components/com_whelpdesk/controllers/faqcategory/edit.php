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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategory.php');

class FaqcategoryEditWController extends FaqcategoryWController {

    public function  __construct() {
        parent::__construct();
        $this->setDefaultView('edit');
        $this->setType('faqcategory');
    }

    /**
     * @todo
     */
    public function execute($stage)
    {
        // get the dientifier
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'request.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD_FC:UNKNOWN FAQ CATEGORY'));
            return;
        }

        // get the data
        $model = WModel::getInstanceByName('faqcategory');
        $faqcategory = $model->getCategory($id);

        // make sure the data loaded
        if(!$faqcategory) {
            JRequest::setVar('task', 'request.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_FC:UNKNOWN FAQ CATEGORY'));
            return;
        }

        // make sure the FAQ category isn't already checked out
        if ($faqcategory->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_FC:FAQ CATEGORY ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_FC:FAQ CATEGORY ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'faqcategory.list.start');
            return;
        }

        // get the JForm
        $form = $model->getForm($faqcategory, true, 'edit');

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'faqcategories.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the term, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $id = $this->commit($id);
                if ($id !== false) {
                   // successfully saved changes
                   WMessageHelper::message(JText::sprintf('WHD_FC:UPDATED FAQ CATEGORY %s', JRequest::getString('name')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'faqcategories.list.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'faqcategory.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                }
        }

        // get the view
        $view = WView::getInstance('faqcategory', 'form',
                                strtolower(JFactory::getDocument()->getType()));

        // add the default model to the view
        $view->addModel('form', $form, true);

        // check out the record
        $model->checkOut($id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($id) {
        // values to use to edit record
        $post = JRequest::get('POST');

        // commit the changes
        return parent::commit($id, $post);
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
            JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY UNKNOWN'));
        }
        return $id;
    }
}

?>