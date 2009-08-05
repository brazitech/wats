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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomain.php');

class KnowledgedomainDisplayWController extends KnowledgedomainWController {

    public function  __construct() {
        parent::__construct();
        $this->setUsecase('display');
    }

    /**
     * Displays the knowledge domain default page.
     */
    public function execute($stage) {
        $model = WModel::getInstance('knowledgedomain');
        
        // get the KD
        $id = WModel::getId();
        if (!$id) {
            $alias = JRequest::getVar('alias', false);
            if (!is_string($alias) || !strlen($alias)) {
                JError::raiseError('INPUT', JText::_('WHD_KD:UNKNOWN KNOWLEDGE DOMAIN'));
                jexit();
            }
            $knowledgeDomain = $model->getKnowledgeDomainFromAlias($alias);
        } else {
            $knowledgeDomain = $model->getKnowledgeDomain($id);
        }

        // make sure the domain exists
        if (!$knowledgeDomain) {
            JError::raiseError('INPUT', JText::_('WHD_KD:UNKNOWN KNOWLEDGE DOMAIN'));
            jexit();
        }

        // set the knowledge id in the request
        JRequest::setVar('id', $knowledgeDomain->default_page);

        // pass on to the knowledge display controller
        JRequest::setVar('task', 'knowledge.display.start');
    }
    
}

?>
