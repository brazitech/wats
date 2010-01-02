<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class ModelKnowledgerevision extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->setDefaultFilterOrder('k.name');
    }

    public function getKnowledgeRevision($knowledgeId, $revision = false) {
        $db = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('r.*')
             . ' FROM ' . dbTable('knowledge_revision') . ' AS ' . dbName('r')
             . ' WHERE ' . dbName('r.knowledge') . ' = ' . intval($knowledgeId);

        if (!$revision) {
            $sql .= ' AND ' . dbName('r.revision') . ' = ('
                  . 'SELECT MAX(' . dbName('m.revision') . ')'
                  . ' FROM ' . dbTable('knowledge_revision') . ' AS ' . dbName('m')
                  . ' WHERE ' . dbName('m.knowledge') . ' = ' . intval($knowledgeId)
                  . ')';
        } else {
            $sql .= ' AND ' . dbName('r.revision') . ' = ' . intval($revision);
        }
        $db->setQuery($sql);
        
        return $db->loadObject();
    }

}

?>