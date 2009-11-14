<?php
/**
 * @version $Id: glossary.php 127 2009-06-11 13:57:35Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.table');
wimport('helper.alias');

/**
 * Representation of the #__whelpdesk_faqs table
 */
class JTableFaq extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Alias used for generating SEF URIs, maximum size is 100 characters
     *
     * @var string
     */
    public $alias = '';

    /**
     * Question the FAQ answers
     *
     * @var string
     */
    public $question = '';

    /**
     * Answer to the question
     *
     * @var string
     */
    public $answer = '';

    /**
     * State
     *
     * @var String
     */
    public $published = 0;

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
     * Date and time when the term was last modified
     *
     * @var String
     */
    public $modified = null;

    /**
     * FK FAQ Category
     *
     * @var int
     */
    public $category = null;

    /**
     * Number of times revised
     *
     * @var int
     */
    public $version = null;

    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct('#__whelpdesk_faqs', 'id', $database);

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
        $isValid = true;

        // check for question
        if (trim($this->question) == '') {
            $this->setError(JText::_('WHD FAQ QUESTION MISSING'));
            $isValid = false;
        } else {
            // check question is unique
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('faqs') . ' ' .
                   'WHERE ' . dbName('question') . ' = ' . $db->Quote($this->question) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD FAQ QUESTION %s MUST BE UNIQUE', $this->question));
                $isValid = false;
            }
        }

        // check for alias
        if (trim($this->alias) == '') {
            $this->setError(JText::_('WHD FAQ ALIAS MISSING'));
            $isValid = false;
        } elseif (!WAliasHelper::isValid($this->alias)) {
            // check alias characters are acceptable
            $this->setError(JText::_('WHD FAQ ALIAS IS INVALID'));
            $isValid = false;
        } else {
            // check alias is unique
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('faqs') . ' ' .
                   'WHERE ' . dbName('alias') . ' = ' . $db->Quote($this->alias) .
                   ' AND ' . dbName('id') . ' != ' . intval($this->id);
            $db->setQuery($sql);
            if ($db->loadResult() > 0) {
                $this->setError(JText::sprintf('WHD FAQ CATEGORY ALIAS %s MUST BE UNIQUE', $this->alias));
                $isValid = false;
            }
        }

        // check the category
        if ($this->category == 0) {
            // oops, did not select a category
            $this->setError(JText::_('WHD PLEASE SELECT AN FAQ CATEGORY'));
            $isValid = false;
        } else {
            // make sure the selected category exists
            $sql = 'SELECT COUNT(*) ' .
                   'FROM ' . dbTable('faq_categories') . ' ' .
                   'WHERE ' . dbName('id') . ' = ' . $db->Quote($this->category);
            $db->setQuery($sql);
            if ($db->loadResult() == 0) {
                $this->setError(JText::sprintf('WHD FAQ CATEGORY MUST EXIST', $this->alias));
                $isValid = false;
            }
        }

        // check for answer
        if (trim($this->answer) == '') {
            $this->setError(JText::_('WHD FAQ ANSWER MISSING'));
            $isValid = false;
        }

        // let the parent have a look
        if (!parent::check()) {
            $isValid = false;
        }

        return $isValid;
    }

    public function bind($from, $ignore=array()) {
        return parent::bind($from, $ignore);
    }
}

?>
