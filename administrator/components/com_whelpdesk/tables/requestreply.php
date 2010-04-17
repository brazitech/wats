<?php
/**
 * @version $Id: request.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('database.table');
wimport('helper.alias');

/**
 * Representation of the #__whelpdesk_request_replies table
 */
class JTableRequestReply extends WTable {

    /**
     * PK
     *
     * @var int
     */
    public $id = null;

    /**
     * Description
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
     * ID of the request to which the request reply is belongs
     *
     * @var int
     */
    public $request_id;

    function __construct($database)
    {
        parent::__construct("#__whelpdesk_request_replies", "id", $database);

        // prepare default values
        $this->created_by = JFactory::getUser()->get("id");
    }
}
