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
 * Representation of the #__whelpdesk_knowledge_domain table
 */
class JTableKnowledgedomain extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Name of the knowledgedomain
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
    public $created_by = 0;

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
     * Time at which the KD was last modified
     *
     * @var string
     */
    public $modified = '0000-00-00 00:00:00';

    /**
     * The default landing page for this knowledge domain
     *
     * @var int
     */
    public $default_page = 0;

    /**
     * Number of times the KD has been revised
     *
     * @var int
     */
    public $revised = 0;

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct('#__whelpdesk_knowledge_domain', 'id', $database);

        // prepare default values
        $this->created_by = JFactory::getUser()->get('id');
    }

    /**
     *
     * @return boolean
     * @todo implement
     */
    public function check() {
        // initialise return value
        $messages = array();

        // check for name
        if (trim($this->name) == '') {
            $messages[] = (JText::_('WHD KNOWLEDGE DOMAIN NAME MISSING'));
        } else {
            // check name is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('knowledge') . ' ' .
                   'WHERE ' . dbName('name') . ' = ' . $db->Quote($this->name) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $messages[] = (JText::sprintf('WHD KNOWLEDGE DOMAIN NAME %s MUST BE UNIQUE', $this->name));
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $messages[] = (JText::_('WHD KNOWLEDGE DOMAIN ALIAS MISSING'));
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $messages[] = (JText::_('WHD KNOWLEDGE DOMAIN ALIAS IS INVALID'));
        } else {
            // check alias is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('knowledge_domain') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $messages[] = (JText::sprintf('WHD KNOWLEDGE DOMAIN ALIAS %s MUST BE UNIQUE', $this->alias));
            }
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
            @$from['published'] = $from['published'] ? 1 : 0;
        } elseif (is_object($from)) {
            @$from->published = $from->published ? 1 : 0;
        }

        if ($safe) {
            // ignore protected fields
            $ignore[] = 'checked_out';
            $ignore[] = 'checked_out_time';
            $ignore[] = 'revised';
            $ignore[] = 'created';
            $ignore[] = 'created_by';
        }

        return parent::bind($from, $ignore);
    }
    
    /**
     * Inserts a new row if id is zero or updates an existing row in the database table
     *
     * Can be overloaded/supplemented by the child class
     *
     * @access public
     * @param boolean If false, null object variables are not updated
     * @return null|stringnull if successful otherwise returns and error message
     */
    function store($updateNulls = false) {
        // check to see if we are updatring or creating
        $k = $this->_tbl_key;
        $isNew = (!$this->$k);
        
        // let the parent do the database store
        if (parent::store($updateNulls)) {
            // now deal with the access tree...
            $k = $this->_tbl_key;
            if($isNew) {
                try {
                    // add node to the access tree
                    $accessSession = WFactory::getAccessSession();
                    $accessSession->addNode('knowledgedomain', $this->id, $this->name, 'knowledgedomains', 'knowledgedomains');
                } catch (Exception $e) {
                    // uh oh it went wrong... tidy up!
                    $this->delete($this->id);
                    return false;
                }
            }
            
        } else {
            return false;
        }
        
        return true;
    }

    public function reset() {
        parent::reset();
        $this->created_by = JFactory::getUser()->get('id');
    }
}

?>
