<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class DocumentcontainerWModel extends WModel {

    public function  __construct() {
        $this->setName('documentcontainer');
        $this->setDefaultFilterOrder('term');
    }

    public function getParents($id) {
        $id = intval($id);

        $database = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('c.*')
             . ' FROM ' . dbTable('document_containers') . ' AS ' . dbName('c')
             . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t')
             . '     ON ' . dbName('t.identifier') . ' = ' . dbName('c.id')
             . '     AND ' . dbName('t.type') . ' = ' . $database->Quote('documentcontainer')
             . '     AND ' . dbName('t.grp') .  ' = ' . $database->Quote('component')
             . ' WHERE ' . dbName('t.lft') . ' < ('
             . '     SELECT ' . dbName('tl.lft')
             . '      FROM ' . dbTable('document_containers') . ' AS ' . dbName('il')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tl')
             . '          ON ' . dbName('tl.identifier') . ' = ' . dbName('il.id')
             . '          AND ' . dbName('tl.type') . ' = ' . $database->Quote('documentcontainer')
             . '          AND ' . dbName('tl.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('il.id') . ' = ' . $id
             . ') AND ' . dbName('t.rgt') . ' > ('
             . '     SELECT ' . dbName('tr.rgt')
             . '      FROM ' . dbTable('document_containers') . ' AS ' . dbName('ir')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tr')
             . '          ON ' . dbName('tr.identifier') . ' = ' . dbName('ir.id')
             . '          AND ' . dbName('tr.type') . ' = ' . $database->Quote('documentcontainer')
             . '          AND ' . dbName('tr.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('ir.id') . ' = ' . $id
             . ')'
             . ' ORDER BY ' . dbName('t.lft');
        $database->setQuery($sql);

        return $database->loadObjectList();
    }

    public function getPath($id) {
        $id = intval($id);

        $database = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('c.*')
             . ' FROM ' . dbTable('document_containers') . ' AS ' . dbName('c')
             . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t')
             . '     ON ' . dbName('t.identifier') . ' = ' . dbName('c.id')
             . '     AND ' . dbName('t.type') . ' = ' . $database->Quote('documentcontainer')
             . '     AND ' . dbName('t.grp') .  ' = ' . $database->Quote('component')
             . ' WHERE ' . dbName('t.lft') . ' <= ('
             . '     SELECT ' . dbName('tl.lft')
             . '      FROM ' . dbTable('document_containers') . ' AS ' . dbName('il')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tl')
             . '          ON ' . dbName('tl.identifier') . ' = ' . dbName('il.id')
             . '          AND ' . dbName('tl.type') . ' = ' . $database->Quote('documentcontainer')
             . '          AND ' . dbName('tl.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('il.id') . ' = ' . $id
             . ') AND ' . dbName('t.rgt') . ' >= ('
             . '     SELECT ' . dbName('tr.rgt')
             . '      FROM ' . dbTable('document_containers') . ' AS ' . dbName('ir')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tr')
             . '          ON ' . dbName('tr.identifier') . ' = ' . dbName('ir.id')
             . '          AND ' . dbName('tr.type') . ' = ' . $database->Quote('documentcontainer')
             . '          AND ' . dbName('tr.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('ir.id') . ' = ' . $id
             . ')'
             . ' ORDER BY ' . dbName('t.lft');
        $database->setQuery($sql);

        return $database->loadObjectList();
    }

    /**
     * Gets an array of containers held in the specified parent
     *
     * @param int $limit
     * @param int $limitstart
     * @return stdClass[]
     */
    public function getDocumentcontainers($parent = null) {
        // check the parent value
        if ($parent == null) {
            $parent = 1;
        } else {
            $parent = intval($parent);
        }

        // get the terms in the documentcontainer
        $sql = $this->buildDocumentcontainersQuery($parent);
        $database = JFactory::getDBO();
        $database->setQuery($sql);

        return $database->loadObjectList();
    }

    public function getDocuments($parent = null) {
        // check the parent value
        if ($parent == null) {
            $parent = 1;
        } else {
            $parent = intval($parent);
        }

        // get the terms in the documentcontainer
        $sql = $this->buildDocumentsQuery($parent);
        $database = JFactory::getDBO();
        $database->setQuery($sql);

        return $database->loadObjectList();
    }

    /**
     *
     *
     * @param int $parent
     * @return string
     * @see DocumentcontainerWModel::getDocumentcontainers()
     */
    private function buildDocumentcontainersQuery($parent) {
        return 'SELECT ' . dbName('c.*') .
               ' FROM ' . dbTable('document_containers') . ' AS ' . dbName('c') .
               ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t') .
               '     ON ' . dbName('t.identifier') . ' = ' . dbName('c.id') .
               '         AND ' . dbName('t.grp') .   ' = ' . JFactory::getDBO()->Quote('component') .
               '         AND ' . dbName('t.type') .  ' = ' . JFactory::getDBO()->Quote('documentcontainer') .
               '         AND ' . dbName('t.parent_type') .   ' = ' . JFactory::getDBO()->Quote('documentcontainer') .
               '         AND ' . dbName('t.parent_identifier') .  ' = ' . intval($parent) .
               ' ORDER BY ' . dbName('c.name');
    }

    /**
     *
     *
     * @param int $parent
     * @return string
     * @see DocumentcontainerWModel::getDocumentcontainers()
     */
    private function buildDocumentsQuery($parent) {
        return 'SELECT ' . dbName('d.*') . ', IFNULL(' . dbName('i.icon') . ', ' . JFactory::getDBO()->Quote('unknown') . ') ' . ' AS ' . dbName('icon') .
               ' FROM ' . dbTable('documents') . ' AS ' . dbName('d') .
               ' LEFT JOIN ' . dbTable('mime_icon') . ' AS ' . dbName('i') .
               '     ON ' . dbName('d.mime_type') . ' = ' . dbName('i.mime_type') .
               ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t') .
               '     ON ' . dbName('t.identifier') . ' = ' . dbName('d.id') .
               '         AND ' . dbName('t.grp') .   ' = ' . JFactory::getDBO()->Quote('component') .
               '         AND ' . dbName('t.type') .  ' = ' . JFactory::getDBO()->Quote('document') .
               '         AND ' . dbName('t.parent_type') .   ' = ' . JFactory::getDBO()->Quote('documentcontainer') .
               '         AND ' . dbName('t.parent_identifier') .  ' = ' . intval($parent) .
               ' ORDER BY ' . dbName('d.name');
    }

    /**
     * Builds the WHERE clause
     *
     * @return string
     */
    private function buildQueryWhere() {
        // get the application
        $application =& JFactory::getApplication();

        // get the state filter (publishing)
        $state = $this->getFilterState();

        // get the free text search filter
        $search = $this->getFilterSearch();
        $search = JString::strtolower($search);

        // prepare to build WHERE clause as an array
        $where = array();
        $db    =& JFactory::getDBO();

        // check if we are performing a free text search
        if ($search) {
            // make string safe for searching
            $search = '%' . $db->getEscaped($search, true). '%';
            $search = $db->Quote($search, false);
            // add search to $where array
            $where[] = 'LOWER(term) LIKE ' . $search;
        }

        // build the WHERE clause
        if (count($where)) {
            // building from array
            $where = ' WHERE ' . implode(' AND ', $where);
        } else {
            // array is empty... nothing to do!
            $where = "";
        }

        // all done, send the result back
        return $where;
    }

    private function buildQueryOrderBy() {
        // ordering
        $order = $this->getFilterOrder();

        // ordering direction
        $orderDirection = $this->getFilterOrderDirection();

        return ' ORDER BY ' . JFactory::getDBO()->nameQuote($order) . ' ' . $orderDirection;
    }

}

?>