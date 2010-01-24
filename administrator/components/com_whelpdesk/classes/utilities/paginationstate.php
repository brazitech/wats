<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class WPaginationState {

    /**
     * Instances of WPaginationState
     *
     * @var WPaginationState[]
     */
    private static $_instances = array();

    /**
     *
     * @var string
     */
    private $_namespace;

    /**
     *
     * @var int
     */
    private $_total;

    private $_limit;
    private $_limitstart;

    /**
     * Constructor
     */
    public function __construct($namespace, $total) {
        $this->_namespace = $namespace;
        $this->_total     = (int)$total;
        $this->_populateState();
    }

    /**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return	void
	 */
	protected function _populateState()
	{
        // get the application object and define the state context
        $app =& JFactory::getApplication();
        $context = $this->_namespace.'.';

        // get the limit and limitstart
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', 0, 'int');
        if ($app->isSite()) {
            // request based
            $limitstart = JRequest::getInt('limitstart', 0);
        } else {
            // state based
            $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
        }
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $limitstart = ($limitstart > $this->_total) ? 0 : $limitstart;

        // set model state data
        $this->_limit = $limit;
		$this->_limitstart = $limitstart;
	}

    public function getPagination()
    {
        jimport('joomla.html.pagination');
        return new JPagination(
            $this->_total,
            $this->_limitstart,
            $this->_limit
        );
    }

    /**
     * Get the limit for the pagination. Maximum number of items to display on a
     * single page.
     *
     * @param int $default Default number of items to display on a page
     * @return int
     */
    public function getLimit() {
        return $this->_limit;
    }

    /**
     * Gets the limitstart for pagination. Number of item with which to start
     * the pagination.
     *
     * @return int
     */
    public function getLimitStart() {
        return $this->_limitstart;
    }

    /**
     * Gets the total number of items related to the model
     *
     * @return int
     */
    public function getTotal() {
        return $this->_total;
    }

    public static function getInstance($namespace, $total)
    {
        // prepare namespace and key.
        $namespace = preg_replace('~\.+$~', '', $namespace);
        $key = $namespace.':'.$total;

        if (array_key_exists($key, self::$_instances))
        {
            return self::$_instances[$key];
        }

        self::$_instances[$key] = new WPaginationState($namespace, $total);
        
        return self::$_instances[$key];
    }

}
