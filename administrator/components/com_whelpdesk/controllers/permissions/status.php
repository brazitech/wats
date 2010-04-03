<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

class PermissionsStatusWController extends WController {

    public function  __construct() {
        $this->setType('permissions');
        $this->setUsecase('status');
    }

    public function execute($stage) {
        // create the response object
        $response = new stdClass();

        // get the target, request and control
        $response->targetType        = JRequest::getString('targetType');
        $response->targetIdentifier  = JRequest::getString('targetIdentifier');
        $response->requestType       = JRequest::getString('requestType');
        $response->requestIdentifier = JRequest::getString('requestIdentifier');
        $response->controlType       = JRequest::getString('controlType');
        $response->control           = JRequest::getString('control');

        // determine the existing rule
        $response->rule = WFactory::getAccessSession()->getRule($response->requestType, $response->requestIdentifier,
                                                                $response->targetType, $response->targetIdentifier,
                                                                $response->controlType, $response->control);

        // determine actual access
        $response->access = WFactory::getAccessSession()->hasAccess($response->requestType, $response->requestIdentifier,
                                                                $response->targetType, $response->targetIdentifier,
                                                                $response->controlType, $response->control);

        echo json_encode($response);
    }

}

?>