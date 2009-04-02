<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Parses a field identifier and makes it safe for use in a query
 *
 * @param String $field
 * @return String
 */
function dbField($field) {
    // setup static cache
    static $fields;
    if (!$fields) {
        $fields = array();
    }

    // check cache for identifier
    if (!array_key_exists($field, $fields)) {
        $db = JFactory::getDBO();
        $fields[$field] = $db->nameQuote($field);
    }

    // return the goods
    return $fields[$field];
}

/**
 * Parses a table name, automatically prefixes with the stndard helpdesk
 * table prefix.
 *
 * @param String $table
 * @return String
 */
function dbTable($table) {
    // setup static cache
    static $tables;
    if (!$tables) {
        $tables = array();
    }

    // check cache for identifier
    if (!array_key_exists($table, $tables)) {
        $db = JFactory::getDBO();
        $tables[$table] = $db->nameQuote("#__helpdesk_" . $table);
    }

    // return the goods
    return $tables[$table];
}

?>
