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
class JTableDocumentcontainer extends WTable {

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
     * Date and time when the term was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

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
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct('#__whelpdesk_document_containers', 'id', $database);
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
            $this->setError(JText::_('WHD DOCUMENT CONTAINER NAME MISSING'));
            $isValid = false;
        } else {
            // check name is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('document_containers') . ' ' .
                   'WHERE ' . dbName('name') . ' = ' . $db->Quote($this->name) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id) .
                   ' AND ' . dbName('parent') . ' = ' . intval($this->parent);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD DOCUMENT CONTAINER NAME %s MUST BE UNIQUE', $this->name));
                $isValid = false;
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $this->setError(JText::_('WHD DOCUMENT CONTAINER ALIAS MISSING'));
            $isValid = false;
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $this->setError(JText::_('WHD DOCUMENT CONTAINER ALIAS IS INVALID'));
            $isValid = false;
        } else {
            // check alias is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('document_containers') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD DOCUMENT CONTAINER ALIAS %s MUST BE GLOBALLY UNIQUE', $this->alias));
                $isValid = false;
            }
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
