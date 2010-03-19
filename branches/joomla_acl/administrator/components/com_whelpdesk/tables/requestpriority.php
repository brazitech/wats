<?php
/**
 * @version $Id: glossary.php 159 2009-08-04 12:14:32Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.table');
wimport('helper.alias');

/**
 * Representation of the #__whelpdesk_request_priorities table
 */
class JTableRequestPriority extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Name of the priority
     *
     * @var string
     */
    public $name = '';

    /**
     * Description of the priority
     *
     * @var string
     */
    public $description = '';

    /**
     * Date and time when the priority was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * ID of the user who created the priority
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
     * User to whom the priority is checked out
     *
     * @var int
     */
    public $checked_out = 0;

    /**
     * Time at which the priority was checked out
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
     * Date and time when the priority was last modified
     *
     * @var String
     */
    public $modified = null;

    /**
     * Order of priorities (the lower the number the higher priority).
     *
     * @var int
     */
    public $ordering;

    public $access;

    /**
     * Hex colour used to distinguish this priority from others.
     *
     * @var String
     */
    public $colour = '#276dd6';


    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct("#__whelpdesk_request_priorities", "id", $database);

        // prepare default values
        $this->created_by = JFactory::getUser()->get("id");
    }
}
