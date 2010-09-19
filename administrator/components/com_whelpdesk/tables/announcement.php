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
 * Representation of the #__whelpdesk_announcement table
 */
class JTableAnnouncement extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Name of the announcement
     *
     * @var string
     */
    public $name = '';

    /**
     * Description of the announcement
     *
     * @var string
     */
    public $description = '';

    /**
     * Date and time when the announcement was created.
     *
     * @var string
     */
    public $created = '0000-00-00 00:00:00';

    /**
     * ID of the user who created the announcement
     *
     * @var int
     */
    public $created_by = 0;

    /**
     * Date from which the announcement is published
     *
     * @var string
     */
    public $publish_from = '0000-00-00 00:00:00';

    /**
     * Date to which the announcement is published
     *
     * @var string
     */
    public $publish_to = '0000-00-00 00:00:00';

    /**
     * User to whom the announcement is checked out
     *
     * @var int
     */
    public $checked_out = 0;

    /**
     * Time at which the announcement was checked out
     *
     * @var string
     */
    public $checked_out_time = '0000-00-00 00:00:00';

    /**
     * Version of the announcement, incremented on every save
     *
     * @var int
     */
    public $revised = 0;

    /**
     * Date and time when the announcement was last modified
     *
     * @var String
     */
    public $modified = null;

    /**
     * User who last modified the announcement
     *
     * @var int
     */
    public $modified_by = null;


    /**
     *
     * @param JDatabase $database
     * @todo document
     */
    function __construct($database) {
        parent::__construct("#__whelpdesk_announcements", "id", $database);

        // prepare default values
        $this->created_by = JFactory::getUser()->get("id");
    }
}
