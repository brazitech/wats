<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Parses a table name, automatically prefixes with the stndard helpdesk
 * table prefix.
 *
 * @param String $table
 * @return String
 */
function dbTable($table) {
    return dbName($table, true);
}

function dbName($name, $table=false) {
    // dealing with a table; add prefix if necessary
    if ($table && substr($name, 0, 3) != '#__') {
        $name = "#__whelpdesk_" . $name;
    }
    
    // dealing with multipart-name?
    if (strpos($name, '.')) {
        $multipart = array();
        foreach (explode('.', $name) as $part) {
            $multipart[] = dbName($part);
        }
        return implode('.', $multipart);
    }
    
    // setup static cache
    static $names;
    if (!$names) {
        $names = array('*' => '*');
    }

    // check cache
    if (!array_key_exists($name, $names)) {
        $names[$name] = JFactory::getDBO()->nameQuote($name);
    }

    // return the goods
    return $names[$name];
}

?>
