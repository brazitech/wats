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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategory.php');

class FaqcategoryDeleteWController extends FaqcategoryWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('delete');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // what ever happens we will want to return to the FAQ categories list
        JRequest::setVar('task', 'faqcategories.list.start');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JError::raiseNotice('INPUT', JText::_('WHD NO FAQ CATEGORIES SELECTED'));
            return;
        }

        // get the model
        $model = WModel::getInstance('faqcategory');

        // itterate over FAQ categories
        foreach ($cid AS $id) {
            $category = $model->getCategory($id);

            if (!$category) {
                JError::raiseWarning('500', JText::sprintf('WHD_FAQCATEGORY:CANNOT DELETE CATEGORY %d, CATEGORY DOES NOT EXIST', $id));
                continue;
            }

            // check access control
            if (!$this->hasAccess($id)) {
                // no access :(
                JError::raiseWarning('401', JText::sprintf('WHD_FAQCATEGORY:DELETE ACCESS DENIED %s', $category->name));
                continue;
            }

            // make sure the FAQ isn't checked out
            if ($category->isCheckedOut(JFactory::getUser()->get('id'))) {
                JError::raiseWarning('500', 'WHD_FAQCATEGORY:CANNOT DELETE FAQ CATEGORY %s FAQ CATEGORY IS CHECKED OUT', $category->name);
            } else {
                if ($model->delete($category->id)) {
                    WMessageHelper::message(JText::sprintf('WHD DELETED FAQ CATEGORY %s', $category->name));
                } else {
                    JError::raiseWarning('500', 'WHD_FAQCATEGORY:DELETE FAQ CATEGORY %s FAILED', $category->name);
                }
            }
        }

        // list the remianing FAQs
        JRequest::setVar('task', 'faqcategories.list');
    }
}

?>