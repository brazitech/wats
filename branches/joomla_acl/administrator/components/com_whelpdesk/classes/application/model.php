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
     * Cached JForm object
     * 
     * @var JForm
     */
    protected $_form = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
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
        $context = 'com_whelpdesk.model.'.$this->getName().'.';

        // get the limit and limitstart and total
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', 0, 'int');
        if ($app->isSite()) {
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
        $filter_order     = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', $this->getDefaultFilterOrder(), 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir',	'filter_order_Dir',	'DESC', 'word');
        $filter_order_Dir = (strtoupper($filter_order_Dir) == 'DESC') ? 'DESC' : 'ASC';

        // set model state data
        $this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
        $this->setState('total', $total);
        $this->setState('search', $search);
        $this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
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
        return $this->getState('limitstart');
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
        return $this->getState('filter_order');
    }

    public function getDefaultFilterOrder() {
        return $this->_defaultFilterOrder;
    }

    protected function setDefaultFilterOrder($default) {
        $this->_defaultFilterOrder = $default;
    }

    /**
     * Get the order direction (ASC or DESC) filter for this model.
     *
     * @param string $default
     * @return string
     */
    public function getFilterOrderDirection($default='ASC') {
        return $this->getState('filter_order_Dir');
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
     * Method to get a form object.
     *
     * @param   object      $data
     * @param   boolean     $reset      Optional argument to force load a new form.
     * @return  mixed       WForm object on success, False on error.
     */
    function getForm($data=null, $reset=false)
    {
        // Check if we can use cached form
        if ($reset || !$this->_form)
        {
            // Get the form
            wimport('form.form');
            JForm::addFormPath(JPATH_COMPONENT.DS.'models'.DS.'forms');
            JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'form'.DS.'fields');
            $this->_form = &WForm::getInstance($this->getName(), $this->getName(), true, array());

            // Check for an error
            if (JError::isError($this->_form))
            {
                $this->setError($$this->_form->getMessage());
                return false;
            }
        }

        // Load data
        if ($data)
        {
            if (!$this->_form->bind($data))
            {
                throw new WException(JText::_('WAHD:MODEL:COULD NOT BIND DATA WITH FORM OBJECT'), $data);
            }
        }

        return $this->_form;
    }

    /**
     * Method to validate the form data.
     *
     * @param   object  $data   The data to validate.
     * @return  boolean Array of filtered data if valid, false otherwise.
     * @since    1.1
     */
    function validate($data, $bindFilteredData=false)
    {
        // Get the form
        $form = $this->getForm($data, true);

        // Filter and validate the form data
        $data  = $form->filter($data);
        $valid = $form->validate($data);

        // Check for an error.
        if (JError::isError($valid)) {
            $this->setError($valid->getMessage());
            return false;
        }

        // Check the validation results
        if ($valid === false)
        {
            // Get the validation messages from the form.
            foreach ($form->getErrors() as $message) {
                $this->setError($message);
            }

            return false;
        }

        if ($bindFilteredData)
        {
            //@todo
        }

        return $data;
    }

    /**
     * Gets a model of the specified type. Note that models a are cached and 
     * therefore state data in maintained.
     *
     * @param String $name
     * @return WModel
     * @todo add security
    */ 
    public static function getInstanceByName($name) {
        // prepare the model name
        $name = strtolower($name);
        
        if (empty(self::$instances[$name])) {
            // determine path and class name
            $modelPath  = JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . $name . '.php';
            $modelClass = 'Model'.ucfirst($name);

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