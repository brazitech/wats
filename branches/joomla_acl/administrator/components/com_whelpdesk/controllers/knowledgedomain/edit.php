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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomain.php');

class KnowledgedomainEditWController extends KnowledgedomainWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('edit');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAIN EDIT ACCESS DENIED');
            return;
        }

        // get the dientifier
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'knowledgedomains.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN UNKNOWN'));
            return;
        }

        // get the data
        $model = WModel::getInstanceByName('knowledgedomain');
        $knowledgeDomain = $model->getKnowledgeDomain($id);

        // make sure the data loaded
        if(!$knowledgeDomain) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseWarning('INPUT', JText::_('WHD_KD:UNKNOWN DOMAIN'));
            JRequest::setVar('task', 'knowledgedomains.list.start');
            return;
        }

        // make sure the KD isn't already checked out
        if ($knowledgeDomain->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_KD:DOMAIN ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_KD:DOMAIN ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'knowledgedomains.list.start');
            return;
        }

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'knowledgedomains.list.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the KD, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                $id = $this->commit($id);
                if ($id !== false) {
                   // successfullt saved changes
                   WMessageHelper::message(JText::sprintf('WHD_KD:UPDATED DOMAIN %s', JRequest::getString('name')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'knowledgedomains.list.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'knowledgedomain.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                }
        }

        // check if we should show the state buttons
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;

        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'knowledgedomain', WModel::getId(),
                                                        'knowledgedomain', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('knowledgedomain', 'form', $format);

        // add the default model to the view
        $view->addModel('knowledgedomain', $knowledgeDomain, true);
        
        // add the boolean value describing access to change published state
        $view->addModel('canChangeState', $canChangeState);

        // check out the record
        $model->checkOut($id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($id) {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        // check if we should allow state change
        $user          = JFactory::getUser();
        $accessSession = WFactory::getAccessSession();
        $canChangeState = false;
        try {
            $canChangeState = $accessSession->hasAccess('user', $user->get('id'),
                                                        'knowledgedomain', WModel::getId(),
                                                        'knowledgedomain', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        if (!$canChangeState) {
            unset($post['published']);
        }

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
            JRequest::setVar('task', 'knowledgedomain.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD KNOWLEDGE DOMAIN UNKNOWN'));
        }
        return $id;
    }
}

?>