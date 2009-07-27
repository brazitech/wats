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
     * ID of the user who created the term
     *
     * @var int
     */
    public $author = 0;

    /**
     * Date and time when the term was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

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
    public $reset_hits = 0;

    /**
     * Version of the record, incremented on every save
     *
     * @var int
     */
    public $version = 0;

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
        $isValid = true;

        // check for term
        if (trim($this->term) == '') {
            $this->setError(JText::_('WHD_GLOSSARY:TERM MISSING'));
            $isValid = false;
        } else {
            // check term is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('glossary') . ' ' .
                   'WHERE ' . dbName('term') . ' = ' . $db->Quote($this->term) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD_GLOSSARY:TERM %s MUST BE UNIQUE', $this->term));
                $isValid = false;
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $this->setError(JText::_('WHD_DATA:ALIAS MISSING'));
            $isValid = false;
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $this->setError(JText::_('WHD_DATA:ALIAS IS INVALID'));
            $isValid = false;
        } else {
            // check alias is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('glossary') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD_DATA:ALIAS %s MUST BE UNIQUE', $this->alias));
                $isValid = false;
            }
        }

        // check for description
        if (trim($this->description) == '') {
            $this->setError(JText::_('WHD_GLOSSARY:DESCRIPTION MISSING'));
            $isValid = false;
        }

        // let the parent have a look
        if (!parent::check()) {
            $isValid = false;
        }

        return $isValid;
    }

    public function bind($from, $ignore=array()) {
        if (is_array($from)) {
            $from['published'] = $from['published'] ? 1 : 0;
        } elseif (is_object($from)) {
            $from->published = $from->published ? 1 : 0;
        }

        // we will deal with params ourselves
        $ignore[] = 'params';

        return parent::bind($from, $ignore);
    }

    public function reset() {
        parent::reset();
        $this->author = JFactory::getUser()->get('id');
    }
}

?>
