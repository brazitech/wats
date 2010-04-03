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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategories.php');

class FaqcategoriesDisplayWController extends FaqcategoriesWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD FAQ CATEGORIES DISPLAY ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('faqcategory');

        // get the view
        $document = JFactory::getDocument();
		$format   = strtolower($document->getType());
        $view     = WView::getInstance('faqcategories', 'display', $format);

        // add the default model to the view
        $view->addModel('faqcategories', $model->getList(0, 0), true);

        // display the view!
        JRequest::setVar('view', 'display');
        $this->display();
    }

    /**
     *
     * @return String
     * @todo
     
    protected function getAccessTargetIdentifier() {
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'faqcategory.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD FAQ CATEGORY UNKNOWN'));
        }
        return $id;
    }*/
}

?>