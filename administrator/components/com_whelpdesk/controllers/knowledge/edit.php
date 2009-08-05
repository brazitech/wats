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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledge.php');

class KnowledgeEditWController extends KnowledgeWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('edit');
    }

    /**
     * @todo
     */
    public function execute($stage) {
        // get the knowledge
        $knowledge = null;
        $model = WModel::getInstance('knowledge');
        $id = WModel::getId();
        if (!$id) {
            // try the alias and domain instead
            $alias = JRequest::getVar('alias', false);
            $domain    = JRequest::getVar('domain', false);
            if (!is_string($alias) || !strlen($alias) || !is_string($domain) || !strlen($domain)) {
                JError::raiseError('INPUT', JText::_('WHD_KD:UNKNOWN KNOWLEDGE'));
                jexit();
            }
            $knowledge = $model->getKnowledgeFromAlias($alias, $domain);
            $id = $knowledge->id;

        } else {
            $knowledge = $model->getKnowledge($id);
        }

        // make sure the data loaded
        if(!$knowledge) {
            JError::raiseError('404', JText::_('WHD_KD:UNKNOWN KNOWLEDGE'));
            jexit();
        }

        // SECURITY
        $user             = JFactory::getUser();
        $accessSession    = WFactory::getAccessSession();
        $canEdit = false;
        try {
            $canEdit = $accessSession->hasAccess('user', $user->get('id'),
                                                          'knowledgedomain', $knowledge->domain,
                                                          'knowledge', 'edit');
        } catch (Exception $e) {
            $canEdit = false;
        }
        if (!$canEdit) {
            JError::raiseWarning('INPUT', JText::_('WHD_KD:KNOWLEDGE DISPLAY ACCESS DENIED'));
            return;
        }

        // make sure the knowledge isn't already checked out
        if ($knowledge->isCheckedOut(JFactory::getUser()->get('id'))) {
            WFactory::getOut()->log('WHD_KNOWLEDGE:KNOWLEDGE ALREADY CHECKEDOUT');
            JError::raiseWarning('500', 'WHD_KNOWLEDGE:KNOWLEDGE ALREADY CHECKEDOUT');
            JRequest::setVar('task', 'knowledge.display.start');
            return;
        }

        // check where in the usecase we are
        switch ($stage) {
            case 'cancel':
                // stop editing, checkin the record
                $model->checkIn($id);
                JRequest::setVar('task', 'knowledge.display.start');
                return;
                break;
            case 'save':
            case 'apply':
                // before saving or applying the term, make sure the token is valid
                shouldHaveToken();

                // attempt to save
                if ($this->commit($id)) {
                   // successfully saved changes
                   WMessageHelper::message(JText::sprintf('WHD_KNOWLEDGE:UPDATED KNOWLEDGE %s', JRequest::getString('term')));
                   if ($stage == 'save') {
                       JRequest::setVar('task', 'knowledge.display.start');
                       $model->checkIn($id);
                   } else {
                       JRequest::setVar('task', 'knowledge.edit.start');
                       JRequest::setVar('id',   $id);
                   }
                   return;
                }
        }

        // get the domain
        $modelDomain = WModel::getInstance('knowledgedomain');
        $knowledgeDomain = $modelDomain->getKnowledgeDomain($knowledge->domain);

        // get the latest knowledge revision
        $modelRevision = WModel::getInstance('knowledgerevision');
        $knowledgeRevision = $modelRevision->getKnowledgeRevision($knowledge->id);

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('knowledge', 'form', $format);

        // add the knowledge
        $view->addModel('knowledge', $knowledge, true);
        $view->addModel('knowledgeDomain', $knowledgeDomain);
        $view->addModel('knowledgeRevision', $knowledgeRevision);

        // check out the record before continuing
        $model->checkOut($id);

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    public function commit($id) {
        $model = WModel::getInstance('knowledge');

        // get the data and make safe
        $post = JRequest::get('POST');
        unset($post['id']);
        unset($post['content']);
        unset($post['comment']);
        unset($post['domain']);
        unset($post['checked_out']);
        unset($post['checked_out_time']);
        unset($post['modified']);
        unset($post['created']);
        unset($post['created_by']);
        unset($post['alias']);
        $post['alias'] = JRequest::getString('newAlias');

        // update the knowledge
        try {
            // attempt to save the data and add a new revision
            $id = $model->save($id, $post);
            $model->revise(
                $id,
                JRequest::getString('content', '', 'POST', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM),
                JRequest::getString('comment', '', 'POST', JREQUEST_ALLOWHTML)
            );
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_KNOWLEDGE:INVALID KNOWLEDGE DATA'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return true;
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        $id = intval(WModel::getId());
        $db = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('domain')
             . ' FROM ' . dbTable('knowledge')
             . ' WHERE ' . dbName('id') . ' = ' . $db->Quote($id);
        $db->setQuery($sql);
        return $db->loadResult();
    }
}

?>