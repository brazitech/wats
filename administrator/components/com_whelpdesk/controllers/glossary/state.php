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
 * Import parent class GlossaryWController
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * Changes the published state of one or more glossary terms.
 */
class GlossaryStateWController extends GlossaryWController {

    public function  __construct() {
        $this->setDefaultView('state');
    }

    /**
     * Publishes glossary terms. This controller does not take any account of
     * the checked out status of glossary terms. This is because this is only a
     * simple data edit and users who have checked out a term may not
     * necessarily have the necessary access to change the state of the term
     * anyway.
     *
     * @param string $stage The stage in the use case, this should be 'publish' or 'unpublish'
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('403', 'WHD_GLOSSARY:STATE ACCESS DENIED');
            return;
        }

        // before saving or applying the new term, make sure the token is valid
        shouldHaveToken();

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid)) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_GLOSSARY:NO TERMS SELECTED'));
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('glossary');

        // itterate over glossary terms
        $unknownTerms = 0;
        foreach($cid AS $id) {
            $term = $model->getTerm($id);
            if(!$term) {
                // term failed to load, assume it is unknown
                $unknownTerms++;
            } elseif($term->isCheckedOut(JFactory::getUser()->get('id'))) {
                // term is checked out - cannot delete
                JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %s IS CHECKEDOUT', $term->term));
            } elseif($term->published == 1 && ($stage == 'publish')) {
                // term is checked out - cannot delete
                JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %s IS ALREADY PUBLISHED', $term->term));
            } elseif($term->published == 0 && ($stage != 'publish')) {
                // term is checked out - cannot delete
                JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM %s IS ALREADY UNPUBLISHED', $term->term));
            } else {
                // okay to delete the term!
                $model->changeState($id, ($stage == 'publish'));
                if ($stage == 'publish') {
                    WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:PUBLISHED TERM %s', $term->term));
                } else {
                    WMessageHelper::message(JText::sprintf('WHD_GLOSSARY:UNPUBLISHED TERM %s', $term->term));
                }
            }
        }

        if ($unknownTerms == 1) {
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:TERM DOES NOT EXIST', $unknownTerms));
        } elseif ($unknownTerms > 1) {
            JError::raiseWarning('500', JText::sprintf('WHD_GLOSSARY:%D TERMS DO NOT EXIST', $unknownTerms));
        }

        // return to the edit screen
        JRequest::setVar('task', 'glossary.list.start');
    }
}

?>