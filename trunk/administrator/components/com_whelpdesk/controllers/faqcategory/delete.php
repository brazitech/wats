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
        // what ever happens we will want to return to the FAQ categpries list
        JRequest::setVar('task', 'faqcategories.list.start');

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JError::raiseNotice('INPUT', JText::_('WHD NO FAQ CATEGORIES SELECTED'));
            return;
        }

        $table = WFactory::getTable('faqcategory');

        // itterate over FAQ categories
        foreach ($cid AS $id) {
            $table->load($id);

            // check access control
            if (!$this->hasAccess($id)) {
                // no access :(
                JError::raiseWarning('401', JText::sprintf('WHD FAQ CATEGORY %s DELETE ACCESS DENIED', $table->name));
                continue;
            }

            // make sure the FAQ isn't checked out
            if ($table->isCheckedOut(JFactory::getUser()->get('id'))) {
                JError::raiseWarning('500', 'WHD FAQ CATEGORY CANNOT DELETE FAQ CATEGORY %s FAQ CATEGORY IS CHECKED OUT', $table->name);
            } else {
                // attempt to delete the node and FAQ category
                // note that the treeSession will deal with the FAQ category
                // delete as well as the node delete
                $accessSession = WFactory::getAccessSession();
                try {
                    // delete the nodes from the tree that represent the category
                    $accessSession->deleteNode('faqcategory', $id, true);
                    WMessageHelper::message(JText::sprintf('WHD DELETED FAQ CATEGORY %s', $table->name));
                } catch (Exception $e) {
                    JError::raiseWarning('500', 'WHD DELETE FAQ CATEGORY %s FAILED', $table->question);
                }
            }
        }

        // list the remianing FAQs
        JRequest::setVar('task', 'faqcategories.list');
    }
}

?>