<?php
/**
 * @version $Id: glossary.php 159 2009-08-04 12:14:32Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.tablenested');
wimport('helper.alias');

/**
 * Representation of the #__whelpdesk_glossary table
 */
class JTableRequestCategory extends WTableNested {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Name of the request category
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

    public $metakey = "";

    public $metadesc = "";


    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct("#__whelpdesk_request_categories", "id", $database);

        // prepare default values
        $this->created_by = JFactory::getUser()->get("id");
    }
}

?>
