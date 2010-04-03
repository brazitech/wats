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

class PermissionsEditWController extends WController {

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

        // check if we should know the request nodes
        $this->requestType = JRequest::getString('requestType');
        if ($stage == 'setPermissions' || $stage == 'savePermissions') {
            $this->requestIdentifiers = JRequest::getVar('requestIdentifiers', array(), 'POST', 'ARRAY');
            JArrayHelper::toInteger($this->requestIdentifiers);
            
            if (!count($this->requestIdentifiers)) {
                // hmmm no request items selected, make the user try again
                $stage = 'selectRequestNodeType';
                JError::raiseWarning(500, 'PLEASE SELECT AT LEAST ONE REQUEST NODE');
            } else {
                // itterate over the request items
                for ($i = 0, $c = count($this->requestIdentifiers) ; $i < $c ; $i++) {
                    // check permissions for each request node
                    if (!$this->hasAccess($this->requestIdentifiers[$i], $this->requestType,
                                          'setpermissions', 'usergroup')) {
                        JError::raiseWarning(401, 'YOU DO NOT HAVE PERMISSION TO SET PERMISSIONS FOR AT LEAST ONE SELECTED REQUEST NODE');
                        return;
                    }
                }
            }
        }

        // perform away...
        switch ($stage) {
            case 'start':
            case 'selectRequestNodeType':
                $this->selectRequestNodeType();
                break;

            case 'findUserRequestNode':
                    $this->findUserRequestNode();
                break;

            case 'findGroupRequestNode':
                    $this->findGroupRequestNode();
                break;

            case 'setPermissions':
                $this->setPermissions();
                break;

            case 'savePermissions':
                $this->savePermissions();
                break;

            default:
                JError::raiseError(500, 'UNKNOWN PERMISSIONS EDIT STAGE '.$stage);
                jexit();
                break;
        }
    }

    private function selectRequestNodeType() {
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view = WView::getInstance('permissions', 'type', $format);

        // add the default model to the view
        $view->addModel('targetType',       $this->targetType);
        $view->addModel('targetIdentifier', $this->targetIdentifier);
        $view->addModel('targetIdentifierAlias', $this->targetIdentifierAlias);
        $view->addModel('returnURI',        $this->returnURI);

        // display the view!
        JRequest::setVar('view', 'type');
        $this->display();
    }

    private function findUserRequestNode() {
        $database = JFactory::getDBO();

        // find all the user groups
        $sql = 'SELECT ' . dbName('g.id') . ', ' . dbName('g.name') . ', ' . dbName('g.alias') . ', ' . dbName('g.description')
             . ' FROM ' . dbTable('user_groups') . ' AS ' . dbName('g');
        $database->setQuery($sql);
        $allGroups = $database->loadObjectList();

        // see which groups we have rights to set permissions on
        $groups = array();
        foreach ($allGroups AS $group) {
            if ($this->hasAccess($group->id, 'usergroup',
                                 'setpermissions', 'usergroup')) {
                $groups[] = $group;
            }
        }

        // check we have at least one group on which we can set the permissions
        if (!count($groups)) {
            // uh oh, no rights on any of the groups!
            JError::raiseWarning('500', 'YOU DO NOT HAVE PERMISSION TO SET PERMISSIONS FOR ANY REQUEST NODE');
            return;
        }

        // get the users
        $whereGroup = array(JRequest::getInt('filterGroup', 0));
        if (!$whereGroup[0]) {
            $whereGroup = JArrayHelper::getColumn($groups, 'id');
        }
        $sql = 'SELECT ' . dbName('u.id') . ', ' . dbName('u.name') . ', ' . dbName('u.username')
             . ', ' . dbName('g.id') . ' AS ' . dbName('group_id') . ', ' . dbName('g.name') . ' AS ' . dbName('group_name') . ', ' . dbName('g.alias') . ' AS ' . dbName('group_alias')
             . ' FROM ' . $database->nameQuote('#__users') . ' AS ' . dbName('u')
             . ' JOIN ' . dbTable('tree') . ' AS ' . dbName('t')
             . '    ON ' . dbName('t.identifier') . ' = ' . dbName('u.id')
             . '    AND ' . dbName('t.type') . ' = ' . $database->Quote('user')
             . ' JOIN ' . dbTable('user_groups') . ' AS ' . dbName('g')
             . '    ON ' . dbName('g.id') . ' = ' . dbName('t.parent_identifier')
             . ' WHERE (' . dbName('g.id') . ' = '
             . implode(' OR ' . dbName('g.id') . ' = ', $whereGroup)
             . ')';
        // add filter if necessary
        $filterSearch = JRequest::getString('filterSearch');
        if (strlen($filterSearch)) {
            $quotedFilterSearch = $database->Quote('%' . $escaped = $database->getEscaped($filterSearch, true) . '%', false);
            $sql .= ' AND ('
                  . dbName('u.username') . ' LIKE ' . $quotedFilterSearch . ' OR '
                  . dbName('u.name') . ' LIKE ' . $quotedFilterSearch
                  . ') ';
        }
        $sql .= ' ORDER BY ' . dbName('u.username') . ' ASC ';

        $database->setQuery($sql);
        $users = $database->loadObjectList();

        // get pagination
        //$pagination = $usersModel->getPagination();

        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view = WView::getInstance('permissions', 'user', $format);

        // add the users to the view
        $view->addModel('users', $users, true);

        // add the models to the view
        $view->addModel('filterSearch',     $filterSearch);
        $view->addModel('selectedGroup',    JRequest::getInt('filterGroup', 0));
        $view->addModel('groups',           $groups);
        $view->addModel('targetType',       JRequest::getString('targetType'));
        $view->addModel('targetIdentifier', JRequest::getString('targetIdentifier'));
        $view->addModel('targetIdentifierAlias', $this->targetIdentifierAlias);
        $view->addModel('returnURI',        $this->returnURI);

        // display the view!
        JRequest::setVar('view', 'user');
        $this->display();
    }
    
    private function findGroupRequestNode() {
        ;
    }

    private function setPermissions() {
        // get the view
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
        $view = WView::getInstance('permissions', 'setpermissions', $format);

        // find all users/user groups
        $database = JFactory::getDBO();
        $sql = 'SELECT ' . dbName('u.*')
             . ' FROM ' . $database->nameQuote('#__users') . ' AS ' . dbName('u')
             . ' WHERE ' . dbName('u.id'). ' = ' . implode(' OR ' . dbName('u.id'). ' = ', $this->requestIdentifiers)
             . ' ORDER BY ' . dbName('u.name') . ', ' . dbName('u.username');
        $database->setQuery($sql);
        $users = $database->loadObjectList();

        $accessSession = WFactory::getAccessSession();
        $accessTree = $accessSession->getAccessNodes('helpdesk', 'display');

        // add the models to the view
        $view->addModel('accessTree', $accessTree, true);

        // request and target stuff
        $view->addModel('targetType',            $this->targetType);
        $view->addModel('targetIdentifier',      $this->targetIdentifier);
        $view->addModel('targetIdentifierAlias', $this->targetIdentifierAlias);
        $view->addModel('returnURI',             $this->returnURI);
        $view->addModel('requestIdentifiers',    $this->requestIdentifiers);

        $view->addModel('users', $users);

        // display the view!
        JRequest::setVar('view', 'setpermissions');
        $this->display();
    }

    private function savePermissions() {

        // get the access tree
        $accessSession = WFactory::getAccessSession();
        $accessTree = $accessSession->getAccessNodes('helpdesk', 'display');

        // get pemrissions data
        $permissions = JRequest::getVar('permissions', array(), 'POST', 'ARRAY');

        $this->saveControlPermissions($accessTree, $permissions);

        JFactory::getApplication()->redirect(htmlspecialchars_decode($this->returnURI), JText::_('UPDATED PERMISSIONS'));
    }

    private function saveControlPermissions($controls, $permissions, $canChange = false) {
        $accessSession = WFactory::getAccessSession();

        // itterate over tree
        foreach ($controls as $control) {
            // determine if this node can be altered
            $canChangeThisNode = ($canChange || $this->targetType == $control->type);

            if ($canChangeThisNode) {
                if (array_key_exists($control->type, $permissions) &&
                    array_key_exists($control->identifier, $permissions[$control->type])) {

                    // node can be altered and there is a corresponding value in $permissions
                    // itterate over request objects
                    foreach ($this->requestIdentifiers as $requestIdentifier) {

                        try {
                            switch ($permissions[$control->type][$control->identifier]) {
                                case '0':
                                    // inherit
                                    $accessSession->clearAccess($this->requestType, $requestIdentifier,
                                                                $this->targetType, $this->targetIdentifier,
                                                                $control->type, $control->identifier);
                                    break;
                                case '-1':
                                    // deny
                                    $accessSession->setAccess($this->requestType, $requestIdentifier,
                                                          $this->targetType, $this->targetIdentifier,
                                                          $control->type, $control->identifier,
                                                          false);
                                    break;
                                case '+1':
                                    // allow
                                    $accessSession->setAccess($this->requestType, $requestIdentifier,
                                                              $this->targetType, $this->targetIdentifier,
                                                              $control->type, $control->identifier,
                                                              true);
                                    break;
                                default:
                                    // no change!
                                    break;
                            }
                        } catch (WException $e) {
                            JError::raiseWarning('500', JText::sprintf('COULD NOT UPDATE %s %s ACCESS FOR %s %s TO %s %s ', $control->type, $control->identifier, $this->requestType, $requestIdentifier, $this->targetType, $this->targetIdentifier));
                        }
                    }
                }
            }

            // act recursivley, deal with children
            if (count($control->children) > 0) {
                $this->saveControlPermissions($control->children, $permissions, $canChangeThisNode);
            }
        }
    }

}

?>