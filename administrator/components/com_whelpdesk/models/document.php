<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class ModelDocument extends WModel {

    public function  __construct() {
        parent::__construct();
        $this->setDefaultFilterOrder('term');
    }

    public function getParents($id) {
        $id = intval($id);

        $database = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('c.*')
             . ' FROM ' . dbTable('documents') . ' AS ' . dbName('c')
             . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t')
             . '     ON ' . dbName('t.identifier') . ' = ' . dbName('c.id')
             . '     AND ' . dbName('t.type') . ' = ' . $database->Quote('documentcontainer')
             . '     AND ' . dbName('t.grp') .  ' = ' . $database->Quote('component')
             . ' WHERE ' . dbName('t.lft') . ' < ('
             . '     SELECT ' . dbName('tl.lft')
             . '      FROM ' . dbTable('documents') . ' AS ' . dbName('il')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tl')
             . '          ON ' . dbName('tl.identifier') . ' = ' . dbName('il.id')
             . '          AND ' . dbName('tl.type') . ' = ' . $database->Quote('document')
             . '          AND ' . dbName('tl.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('il.id') . ' = ' . $id
             . ') AND ' . dbName('t.rgt') . ' > ('
             . '     SELECT ' . dbName('tr.rgt')
             . '      FROM ' . dbTable('documents') . ' AS ' . dbName('ir')
             . '      JOIN ' . dbTable('tree') . ' AS ' . dbName('tr')
             . '          ON ' . dbName('tr.identifier') . ' = ' . dbName('ir.id')
             . '          AND ' . dbName('tr.type') . ' = ' . $database->Quote('document')
             . '          AND ' . dbName('tr.grp') .  ' = ' . $database->Quote('component')
             . '      WHERE ' . dbName('ir.id') . ' = ' . $id
             . ')'
             . ' ORDER BY ' . dbName('t.lft');


        $sql = 'SELECT ' . dbName('containers.*')
             . ' FROM ' . dbTable('tree') . ' AS ' . dbName('document')
             . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('tree')
             . '     ON ' . dbName('tree.type') . ' = ' . $database->Quote('documentcontainer')
             . '     AND ' . dbName('tree.grp') . ' = ' . $database->Quote('component')
             . ' JOIN ' . dbTable('document_containers') . ' AS ' . dbName('containers')
             . '     ON ' . dbName('containers.id') . ' = ' . dbName('tree.identifier')
             . ' WHERE ' . dbName('document.identifier') . ' = ' . $id
             . '     AND ' . dbName('document.type')     . ' = ' . $database->Quote('document')
             . '     AND ' . dbName('document.grp')      . ' = ' . $database->Quote('component')
             . '     AND ' . dbName('tree.lft')          . ' < ' . dbName('document.lft')
             . '     AND ' . dbName('tree.rgt')          . ' > ' . dbName('document.rgt')
             . ' ORDER BY ' . dbName('tree.lft');


        $database->setQuery($sql);

        return $database->loadObjectList();
    }

    public static function getIcon($mimeType) {
        $database = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('icon')
             . ' FROM ' . dbTable('mime_icon')
             . ' WHERE ' . dbName('mime_type') . ' = ' . $database->Quote($mimeType);
        $database->setQuery($sql);

        $icon = $database->loadResult();
        return $icon ? $icon : 'unknown';
    }

}

?>