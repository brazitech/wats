<?php
/**
 * @version $Id: glossary.php 121 2009-05-29 12:57:24Z webamoeba $
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
class JTableDocument extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * The ID of the parent container, if this is 0 this is the root document
     * container. The root document container always has an ID of 1 and cannot
     * be deleted.
     *
     * @var int
     */
    public $parent = null;

    /**
     * Term that the record defines, maximum size is 500 characters
     *
     * @var string
     */
    public $name = '';

    /**
     * Alias used for generating SEF URIs, maximum size is 100 characters
     *
     * @var string
     */
    public $alias = '';

    /**
     * Optional description of the container
     *
     * @var string
     */
    public $description = '';

    /**
     * Date and time when the document was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * User who the document was uploaded by
     *
     * @var int
     */
    public $created_by;

    /**
     * Date and time when the document was modified.
     *
     * @var string
     */
    public $modified = '0000-00-00 00:00:00';

    /**
     * User to whom the document is checked out
     *
     * @var int
     */
    public $checked_out = 0;

    /**
     * Time at which the document was checked out
     *
     * @var string
     */
    public $checked_out_time = '0000-00-00 00:00:00';

    /**
     * Number of times the document has been downloaded
     *
     * @var int
     */
    public $hits = 0;

    /**
     * Time at which the hits were last reset
     *
     * @var string
     */
    public $hits_reset = '0000-00-00 00:00:00';

    /**
     * The MIME type of the payload
     *
     * @var string
     */
    public $mime_type = '';

    /**
     * Name of the file as it should be when downloaded - this is generllay the
     * orginal filename.
     *
     * @var string
     */
    public $filename = '';

    /**
     * The size of the payload measured in bytes
     *
     * @var int
     */
    public $bytes = 0;

    /**
     * The file contents
     *
     * @var string
     */
    public $payload = '';

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct('#__whelpdesk_documents', 'id', $database);
    }

    /**
     *
     * @return boolean
     * @todo implement
     */
    public function check() {
        // initialise return value
        $isValid = true;

        // check the parent ID is an integer
        if (!preg_match('~^0-9+$~', $this->parent)) {
            new WException('POTENTIAL SECURITY BREACH DETECTED');
        }

        // check for name
        if (trim($this->name) == '') {
            $this->setError(JText::_('WHD DOCUMENT NAME MISSING'));
            $isValid = false;
        } else {
            // check name is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('documents') . ' ' .
                   'WHERE ' . dbName('name') . ' = ' . $db->Quote($this->name) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id) .
                   ' AND ' . dbName('parent') . ' = ' . intval($this->parent);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD DOCUMENT NAME %s MUST BE UNIQUE', $this->name));
                $isValid = false;
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $this->setError(JText::_('WHD DOCUMENT ALIAS MISSING'));
            $isValid = false;
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $this->setError(JText::_('WHD DOCUMENT ALIAS IS INVALID'));
            $isValid = false;
        } else {
            // check alias is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('documents') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD DOCUMENT ALIAS %s MUST BE GLOBALLY UNIQUE', $this->alias));
                $isValid = false;
            }
        }

        // check for file name
        if (trim($this->filename) == '') {
            $this->setError(JText::_('WHD DOCUMENT FILENAME MISSING'));
            $isValid = false;
        }

        // let the parent have a look
        if (!parent::check()) {
            $isValid = false;
        }

        return $isValid;
    }

    public function bind($from, $ignore=array()) {
        // we will deal with params ourselves
        $ignore[] = 'params';

        return parent::bind($from, $ignore);
    }
}

?>
