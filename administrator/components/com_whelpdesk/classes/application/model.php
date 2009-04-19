<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('getinstance');

abstract class WModel {

    /**
     * Instances of WModel
     * 
     * @var WModel
     */
    private static $instances = array();

    /**
     * Name of the model
     *
     * @var String
     */
    private $name;

    /**
     * Maximum number of items to display on a single page.
     *
     * @var int
     */
    private $limit = null;

    /**
     * Number of item with which to start the pagination.
     *
     * @var int
     */
    private $limitstart = null;

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
         // determine limitstart if we don't already know what it is
        if ($this->limit === null) {
            $application =& JFactory::getApplication();

            // state based
            $this->limit = $application->getUserStateFromRequest('com_whelpdesk.model.' . $this->getName() . '.limit',
                                                                 'limit');
        }

        return $this->limit;
    }

    /**
     * Gets the limitstart for pagination. Number of item with which to start
     * the pagination.
     *
     * @return int
     */
    public function getLimitStart() {
        // determine limitstart if we don't already know what it is
        if ($this->limiststart === null) {
            $application =& JFactory::getApplication();

            if ($application->isSite()) {
                // request based
                $this->limitstart = JRequest::getInt('limitstart', 0);
            } else {
                // state based
                $this->limitstart = $application->getUserStateFromRequest('com_whelpdesk.model.' . $this->name . '.limitstart',
                                                                          'limitstart',
                                                                          0);
            }
        }

        return $this->limitstart;
    }

    /**
     * Gets the total number of items related to the model
     * 
     * @return int
     */
    public function getTotal() {
        throw new WException("METHOD NOT IMPLEMENTED");
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
    public static function getId() {
        // attempt to get the ID normally
        $id = JRequest::getInt('id');

        // no luck?  try the cid array
        if (!$id) {
            $cid = JRequest::getVar('cid');
            $id = (int)$cid[0];
        }

        return $id;
    }

    /**
     * Get the array of IDs passed in the request. Returns an empty array on
     * fail.
     *
     * @return int
     */
    public static function getAllIds() {
        // attempt to get the ID normally
        $cid = JRequest::getVar('cid');

        if (!is_array($cid)) {
            // no luck?  go for an empty array
            $cid = array();
        } else {
            // make sure the IDs are safe
            for ($i = 0, $c = count($cid); $i < $c; $i++) {
                $cid[$i] = intval($cid[$i]);
            }
        }

        return $cid;
    }
}

?>