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
class RequestPriorityStateWController extends WController {

    public function  __construct() {
        $this->setUsecase('state');
    }

    public function execute($stage) {
        // before saving or applying the new term, make sure the token is valid
        shouldHaveToken();

        // load the IDs
        $cid = WModel::getAllIds();
        if (!count($cid))
        {
            JRequest::setVar('task', 'requestpriority.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_RP:NO PRIORITIES SELECTED'));
            return;
        }

        // get the model
        $model = WModel::getInstanceByName('requestpriority');

        // itterate over glossary terms
        $unknownPriorities = 0;
        foreach($cid AS $id)
        {
            $table = $model->getTable($id, true);
            if(!$table)
            {
                // priority failed to load, assume it is unknown
                $unknownPriorities++;
            }
            elseif($table->isCheckedOut(JFactory::getUser()->get('id')))
            {
                // priority is checked out - cannot change state
                JError::raiseWarning('500', JText::sprintf('WHD_RP:REQUEST PRIORITY %s IS CHECKEDOUT', $table->name));
            }
            elseif($table->published == 1 && ($stage == 'publish'))
            {
                // priority is already published
                JError::raiseWarning('500', JText::sprintf('WHD_RP:REQUEST PRIORITY %s IS ALREADY PUBLISHED', $table->name));
            }
            elseif($table->published == 0 && ($stage != 'publish'))
            {
                // priority is already unpublished
                JError::raiseWarning('500', JText::sprintf('WHD_RP:REQUEST PRIORITY %s IS ALREADY UNPUBLISHED', $table->name));
            }
            else
            {
                // okay to delete the term!
                $model->changeState($id, ($stage == 'publish'));
                if ($stage == 'publish')
                {
                    WMessageHelper::message(JText::sprintf('WHD_RP:PUBLISHED REQUEST PRIORITY %s', $table->name));
                }
                else
                {
                    WMessageHelper::message(JText::sprintf('WHD_RP:UNPUBLISHED REQUEST PRIORITY %s', $table->name));
                }
            }
        }

        // add warning for unknown prioorities
        if ($unknownPriorities == 1)
        {
            JError::raiseWarning('500', JText::sprintf('WHD_RP:ONE REQUEST PRIORITY DOES NOT EXIST', $unknownPriorities));
        }
        elseif ($unknownPriorities > 1)
        {
            JError::raiseWarning('500', JText::sprintf('WHD_RP:%d REQUEST PRIORITIES DO NOT EXIST', $unknownPriorities));
        }

        // return to the edit screen
        JRequest::setVar('task', 'requestpriority.list.start');
    }
}
