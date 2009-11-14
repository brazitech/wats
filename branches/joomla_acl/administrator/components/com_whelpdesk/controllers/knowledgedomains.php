<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'knowledgedomain.php');

abstract class KnowledgedomainsWController extends KnowledgedomainWController {

    public function __construct() {
        parent::__construct();
        $this->setType('knowledgedomains');
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return $this->getType();
    }
    
}

?>