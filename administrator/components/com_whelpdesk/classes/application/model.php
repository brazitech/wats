<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

jimport('joomla.application.component.model');
wimport('getinstance');

abstract class WModel extends JModel {

    /**
     * Instances of WModel
     * 
     * @var WModel
     */
    private static $instances = array();

    /**
     * Default column by which to order lists
     *
     * @var string
     * @see WModel::getDefaultFilterOrder()
     * @see WModel::setDefaultFilterOrder()
     */
    private $_defaultFilterOrder = '';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    
        // get the application object and define the state context
        $app =& JFactory::getApplication();
        $context = 'com_whelpdesk.model.'.$this->getName();
        
        // get the limit and limitstart and total
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', 0, 'int');
        if ($application->isSite()) {
            // request based
            $limitstart = JRequest::getInt('limitstart', 0);
        } else {
            // state based
            $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
        }
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $total = $this->getTotal();
            
        
        // get the filters
        $search           = $app->getUserStateFromRequest($context.'search', 'search', '', 'string');
        $filter_order     = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'a.created', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir',	'filter_order_Dir',	'desc', 'word');
        
        // set model state data
        $this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
        $this->setState('total', $total);
        $this->setState('search', $search);
        $this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
    }

    /**
     * Sets the name of the model
     *
     * @param String $name
     */
    protected function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * Gets the name of the model
     * 
     * @return String
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the limit for the pagination. Maximum number of items to display on a
     * single page.
     *
     * @param int $default Default number of items to display on a page
     * @return int
     */
    public function getLimit() {
        return $this->getState('limit');
    }

    /**
     * Gets the limitstart for pagination. Number of item with which to start
     * the pagination.
     *
     * @return int
     */
    public function getLimitStart() {
        // determine limitstart if we don't already know what it is
        if ($this->limitstart === null) {
            $application =& JFactory::getApplication();

            if ($application->isSite()) {
                // request based
                $this->limitstart = JRequest::getInt('limitstart', 0);
            } else {
                // state based
                $this->limitstart = $application->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.limitstart',
                                                                          'limitstart',
                                                                          0);
            }
            
            // correct value as necessary
            $limit = $this->getLimit();
            $total = $this->getTotal();
            if ($this->limitstart > $total) {
                $this->limitstart = $total;
            }
            $this->limitstart = ($limit != 0 ? (floor($this->limitstart / $limit) * $limit) : 0);
        }

        return $this->limitstart;
    }

    /**
     * Get the current state filter for this model.
     *
     * @param string $default
     * @return string
     */
    public function getFilterState($default='') {
        return JFactory::getApplication()->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.filter.state',
                                                                   'filter_state',
                                                                   $default,
                                                                   'word');
    }

    /**
     * Get the current search filter for this model.
     *
     * @param string $default
     * @return string
     */
    public function getFilterSearch($default='') {
        return JFactory::getApplication()->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.filter.search',
                                                                   'search',
                                                                   $default,
                                                                   'string');
    }

    /**
     * Get the current order by column filter for this model.
     *
     * @return string
     */
    public function getFilterOrder() {
        return JFactory::getApplication()->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.filter.order',
                                                                   'filter_order',
                                                                   $this->getDefaultFilterOrder(),
                                                                   'cmd');
    }

    public function getDefaultFilterOrder() {
        return $this->defaultFilterOrder;
    }

    protected function setDefaultFilterOrder($default) {
        $this->defaultFilterOrder = $default;
    }

    /**
     * Get the order direction (ASC or DESC) filter for this model.
     *
     * @param string $default
     * @return string
     */
    public function getFilterOrderDirection($default='ASC') {
        $direction = JFactory::getApplication()->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.filter.direction',
                                                                        'filter_order_Dir',
                                                                         $default,
                                                                         'cmd');
        return (strtoupper($direction) == 'DESC') ? 'DESC' : 'ASC';
    }

    /**
     *
     * @return array
     */
    public function getFilters() {
        $filters = array();

        $filters['state']           = $this->getFilterState();
        $filters['search']          = $this->getFilterSearch();
        $filters['order']           = $this->getFilterOrder();
        $filters['orderDirection']  = $this->getFilterOrderDirection();
        
        return $filters;
    }

    /**
     * Gets the total number of items related to the model
     * 
     * @return int
     */
    public function getTotal() {
        JError::raiseWarning(JText::sprintf('WAHD.FRAMEWORK.APPLICATION.MODEL:Model %s Method %s Not Implemented', $this->getName(), 'getTotal'));
        return 0;
    }

    /**
     * Gets a model of the specified type. Note that models a are cached and 
     * therefore state data in maintained.
     *
     * @param String $name
     * @return WModel
     * @todo add security
     */
    public static function getInstance($name) {
        // prepare the model name
        $name = strtolower($name);
        
        if (empty(self::$instances[$name])) {
            // determine path and class name
            $modelPath  = JPATH_COMPONENT . DS . 'models' . DS . $name . '.php';
            $modelClass = ucfirst($name) . 'WModel';

            // get the class and make an instance
            require_once $modelPath;
            self::$instances[$name] = new $modelClass();
        }

        return self::$instances[$name];
    }

    /**
     * Get the ID passed in the request. Returns 0 if the ID is not known.
     *
     * @return int
     */
    public static function getId($raw = false) {
        // attempt to get the ID normally
        if ($raw) {
            $id = JRequest::getVar('id');
        } else {
            $id = JRequest::getInt('id');
        }

        // no luck?  try the cid array
        if (!$id) {
            $cid = JRequest::getVar('cid');
            if ($raw) {
                $id = $cid[0];
            } else {
                $id = (int)$cid[0];
            }
        }

        return $id;
    }

    /**
     * Get the array of IDs passed in the request. Returns an empty array on
     * fail.
     *
     * @return int
     */
    public static function getAllIds($raw = false) {
        // attempt to get the ID normally
        $cid = JRequest::getVar('cid');

        if (!is_array($cid)) {
            // no luck?  go for an empty array
            $cid = array();
        } else {
            if (!$raw) {
                // make sure the IDs are safe
                for ($i = 0, $c = count($cid); $i < $c; $i++) {
                    $cid[$i] = intval($cid[$i]);
                }
            }
        }

        return $cid;
    }
}

?>