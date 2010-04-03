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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledge.php');

class KnowledgeDisplayWController extends KnowledgeWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * Displays an item of knowledge
     */
    public function execute($stage) {
        /*try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD KNOWLEDGE DOMAIN DISPLAY ACCESS DENIED');
            return;
        }*/

        // get the knowledge
        $knowledge = null;
        $model = WModel::getInstanceByName('knowledge');
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
            
        } else {
            $knowledge = $model->getKnowledge($id);
        }

        // make sure the data loaded
        if(!$knowledge) {
            JError::raiseError('404', JText::_('WHD_KD:UNKNOWN KNOWLEDGE C'));
            jexit();
        }

        // SECURITY
        $user             = JFactory::getUser();
        $accessSession    = WFactory::getAccessSession();
        $canDisplayKnowledge = false;
        try {
            $canDisplayKnowledge = $accessSession->hasAccess('user', $user->get('id'),
                                                          'knowledgedomain', $knowledge->domain,
                                                          'knowledgedomain', 'display');
        } catch (Exception $e) {
            $canDisplayKnowledge = false;
        }
        if (!$canDisplayKnowledge) {
            JError::raiseWarning('INPUT', JText::_('WHD_KD:KNOWLEDGE DISPLAY ACCESS DENIED'));
            return;
        }

        // get the domain
        $model = WModel::getInstanceByName('knowledgedomain');
        $knowledgeDomain = $model->getKnowledgeDomain($knowledge->domain);

        // get the knowledge revision
        $model = WModel::getInstanceByName('knowledgerevision');
        $knowledgeRevision = $model->getKnowledgeRevision($knowledge->id);

        // check if we should show the knowledge domain edit button
        $canEditKnowledge = false;
        try {
            $canEditKnowledge = $accessSession->hasAccess('user', $user->get('id'),
                                                          'knowledgedomain', $knowledge->domain,
                                                          'knowledgedomain', 'edit');
        } catch (Exception $e) {
            $canChangeState = false;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view     = WView::getInstance('knowledge', 'display', $format);

        // add the knowledge to the view
        $view->addModel('knowledge', $knowledge, true);
        $view->addModel('knowledgeRevision', $knowledgeRevision);
        $view->addModel('knowledgedomain', $knowledgeDomain);

        // add the boolean value describing access to edit knowledge
        $view->addModel('canEditKnowledge', $canEditKnowledge);

        // display the view!
        JRequest::setVar('view', 'display');
        $this->display();
    }

}

?>