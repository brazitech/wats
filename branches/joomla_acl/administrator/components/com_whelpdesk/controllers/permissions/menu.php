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

class PermissionsMenuWController extends WController {

    private $targetType;
    private $targetIdentifier;
    private $targetIdentifierAlias;
    private $returnURI;

    private $requestType;
    private $requestIdentifiers;

    public function  __construct() {
        $this->setType('permissions');
        $this->setUsecase('edit');
    }

    public function execute($stage) {
        // get the bells and whistles
        $this->targetIdentifierAlias = base64_decode(JRequest::getVar('targetIdentifierAlias', '', 'REQUEST', 'BASE64'));
        $this->returnURI = base64_decode(JRequest::getVar('returnURI', '', 'REQUEST', 'BASE64'));

        // get the target
        $this->targetType       = JRequest::getString('targetType');
        $this->targetIdentifier = JRequest::getString('targetIdentifier');

        // check that we have a valid target node
        $accessSession = WFactory::getAccessSession();
        if (!$accessSession->nodeExists($this->targetType, $this->targetIdentifier)) {
            JError::raiseError(500, JText::sprintf('UNKNOWN TARGET NODE %s %s', $this->targetType, $this->targetIdentifier));
            jexit();
        }

        // check that we have the rights to change permissions on the target node
        $controlType = $this->targetType;
        if (!$this->hasAccess($this->targetIdentifier, $this->targetType,
                              'permissions', $controlType)) {
            JError::raiseWarning(401, 'YOU DO NOT HAVE PERMISSION TO CHANGE ACCESS ON TARGET NODE');
            // let the next controller have a go
            $controlPath = $accessSession->getControlPath();
            if (count($controlPath) > 1) {
                $nextControl = $controlPath[1];
                JRequest::setVar('task', $nextControl['type'] . '.' . $nextControl['identifier']);
            }
            return;
        }

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view = WView::getInstance('permissions', 'menu', $format);

        // add the default model to the view
        $view->addModel('targetType',       $this->targetType);
        $view->addModel('targetIdentifier', $this->targetIdentifier);
        $view->addModel('targetIdentifierAlias', $this->targetIdentifierAlias);
        $view->addModel('returnURI',        $this->returnURI);

        // display the view!
        JRequest::setVar('view', 'menu');
        $this->display();
    } 

}

?>