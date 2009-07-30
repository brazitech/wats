<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.table');
wimport('helper.alias');

/**
 * Representation of the #__whelpdesk_glossary table
 */
class JTableGlossary extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Term that the record defines, maximum size is 500 characters
     *
     * @var string
     */
    public $term = '';

    /**
     * Alias used for generating SEF URIs, maximum size is 100 characters
     *
     * @var string
     */
    public $alias = '';

    /**
     * Description of the term
     *
     * @var string
     */
    public $description = '';

    /**
     * Date and time when the term was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * ID of the user who created the term
     *
     * @var int
     */
    public $created_by = 0;

    /**
     * Published state
     *
     * @var int
     */
    public $published = 1;

    /**
     * User to whom the term is checked out
     *
     * @var int
     */
    public $checked_out = 0;

    /**
     * Time at which the term was checked out
     *
     * @var string
     */
    public $checked_out_time = '0000-00-00 00:00:00';

    /**
     * Number of hits (times the term has been uniquely viewed)
     *
     * @var int
     */
    public $hits = 0;

    /**
     * date and time when the hits counter was last reset
     *
     * @var String
     */
    public $hits_reset = '0000-00-00 00:00:00';

    /**
     * user by whom the hits counter was last reset
     *
     * @var String
     */
    public $hits_reset_by = 0;

    /**
     * Version of the record, incremented on every save
     *
     * @var int
     */
    public $revised = 0;

    /**
     * Date and time when the term was last modified
     *
     * @var String
     */
    public $modified = null;
    
    /**
     * Date and time when the term was last modified
     *
     * @var String
     */
    //public $metadata_description = null;

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct("#__whelpdesk_glossary", "id", $database);

        // prepare default values
        $this->author = JFactory::getUser()->get('id');
    }

    /**
     *
     * @return boolean
     * @todo implement
     */
    public function check() {
        // initialise return value
        $messages = array();

        // check for term
        if (trim($this->term) == '') {
            $messages[] = JText::_('WHD_GLOSSARY:TERM MISSING');
        } else {
            // check term is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('glossary') . ' ' .
                   'WHERE ' . dbName('term') . ' = ' . $db->Quote($this->term) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $messages[] = JText::sprintf('WHD_GLOSSARY:TERM %s MUST BE UNIQUE', $this->term);
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $messages[] = JText::_('WHD_DATA:ALIAS MISSING');
            $isValid = false;
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $messages[] = JText::_('WHD_DATA:ALIAS IS INVALID');
        } else {
            // check alias is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('glossary') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $messages[] = JText::sprintf('WHD_DATA:ALIAS %s MUST BE UNIQUE', $this->alias);
            }
        }

        // check for description
        if (trim($this->description) == '') {
            $messages[] = JText::_('WHD_GLOSSARY:DESCRIPTION MISSING');
        }

        // let the parent have a look
        $parentCheck = parent::check();
        if (is_array($parentCheck)) {
            $messages = array_merge($messages, $parentCheck);
        }

        return count($messages) ? $messages : true;
    }

    public function bind($from, $ignore=array(), $safe=false) {
        if (is_array($from)) {
            $from['published'] = $from['published'] ? 1 : 0;
        } elseif (is_object($from)) {
            $from->published = $from->published ? 1 : 0;
        }

        if ($safe) {
            // ignore protected fields
            $ignore[] = 'hits';
            $ignore[] = 'hits_reset';
            $ignore[] = 'hits_reset_by';
            $ignore[] = 'hits_reset';
            $ignore[] = 'checked_out';
            $ignore[] = 'checked_out_time';
            $ignore[] = 'revised';
            $ignore[] = 'created';
            $ignore[] = 'created_by';
        }

        return parent::bind($from, $ignore);
    }

    public function reset() {
        parent::reset();
        $this->author = JFactory::getUser()->get('id');
    }
}

?>
