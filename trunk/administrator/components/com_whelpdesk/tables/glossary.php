<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

jimport('joomla.database.table');

/**
 * Representation of the #__whelpdesk_glossary table
 */
class JTableGlossary extends JTable {

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
     * Extra parameters
     *
     * @var JParameter
     */
    public $params = null;

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct("#__whelpdesk_glossary", "id", $database);

        // prepare default values
        $this->params = new JParameter('', JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'glossary.xml');
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
            $this->setError(JText::_('WHD GLOSSARY TERM MISSING'));
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
                $this->setError(JText::sprintf('WHD GLOSSARY TERM %s MUST BE UNIQUE', $this->term));
                $isValid = false;
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $this->setError(JText::_('WHD GLOSSARY ALIAS MISSING'));
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
                $this->setError(JText::sprintf('WHD GLOSSARY ALIAS %s MUST BE UNIQUE', $this->alias));
                $isValid = false;
            }
        }

        // check for description
        if (trim($this->description) == '') {
            $this->setError(JText::_('WHD GLOSSARY DESCRIPTION MISSING'));
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
}

?>
