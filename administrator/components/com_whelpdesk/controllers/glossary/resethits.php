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
 * Get the parent class GlossaryWController
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * Resets the hits counter for glossary items. Can only deal with a maximum of
 * one term at a time.
 */
class GlossaryResethitsWController extends GlossaryWController {

    /**
     * @todo document
     */
    public function  __construct() {
        parent::__construct();
        $this->setUsecase('resethits');
    }

    /**
     * Resets the hit counter
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD_GLOSSARY:RESET HITS ACCESS DENIED');
            return;
        }

        // get the ID of the term we want to reset the hits for
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD_GLOSSARY:NO TERM SELECTED'));
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('glossary');
        $term = $model->getTerm($id);

        // make sure term is valid
        if (!$term) {
            // term failed to load, assume it is unknown
            JError::raiseNotice('INPUT', JText::_('WHD_GLOSSARY:TERM DOES NOT EXIST'));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }

        // make sure term is not chekced out by anyone else
        if ($term->isCheckedOut(JFactory::getUser()->get('id'))) {
            // term is checked out by another user - cannot reset hits
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %S IS CHECKEDOUT', $term->term));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        }

        // make sure we actually need to do this...
        if (!$term->hits) {
            // term hits are already zero - no need to reset!
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %S HITS ARE ALREADY ZERO', $term->term));
            JRequest::setVar('task', 'glossary.list.start');
            return;
        } else {
            // okay to reset hits
            $model->resetHits($id);
            WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:RESET TERM %s HITS', $term->term));
        }

        // go back to the edit screen
        JRequest::setVar('task', 'glossary.edit.start');
    }
}

?>