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
 * Representation of the #__whelpdesk_knowledge table
 */
class JTableKnowledge extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Name of the knowledge item
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
     * KD to which the item belongs, foreign key.
     *
     * @var int
     */
    public $domain = 0;

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
     * Date and time when the first link to the knowledge was created
     *
     * @var int
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * User who crated the knowledge - i.e. made thye first link to the knowledge
     *
     * @var int
     */
    public $created_by = 0;

    /**
     * Date and time when the first link to the knowledge was created
     *
     * @var int
     */
    public $modified = '0000-00-00 00:00:00';

    /**
     * Extra parameters
     *
     * @var JParameter
     */
    public $params = null;

    /**
     * Number of hits
     *
     * @var int
     */
    public $hits = 0;

    /**
     * Time at which the hits were reset
     *
     * @var string
     */
    public $reset_hits = '0000-00-00 00:00:00';

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct('#__whelpdesk_knowledge', 'id', $database);

        // prepare default values
        $this->params = new JParameter('', JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'knowledge.xml');
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

        // check for name
        if (trim($this->name) == '') {
            $messages[] = JText::_('WHD_KNOWLEDGE:NAME MISSING');
        }

        // check alias is unique to domain
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
                   'FROM ' . dbTable('knowledge') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id) .
                   ' AND ' . dbName('domain') . ' != ' . intval($this->domain);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $messages[] = JText::sprintf('WHD_DATA:ALIAS %s MUST BE UNIQUE', $this->alias);
            }
        }

        // let the parent have a look
        $parentCheck = parent::check();
        if (is_array($parentCheck)) {
            $messages = array_merge($messages, $parentCheck);
        }

        return count($messages) ? $messages : true;
    }

    public function bind($from, $ignore=array()) {
        // we will deal with params ourselves
        $ignore[] = 'params';

        return parent::bind($from, $ignore);
    }
}

?>
