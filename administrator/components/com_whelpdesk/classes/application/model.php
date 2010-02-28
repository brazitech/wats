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
     * @var WList
     */
    protected $_list = null;

    /**
     * Name of the WTable that relates to this model
     *
     * @var String
     */
    protected $_tableName;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
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
     * Method to get a list object.
     *
     * @param   boolean     $reset      Optional argument to force load a new form.
     * @return  mixed       WList       object on success, False on error.
     */
    function getList($reset=false)
    {
        // Check if we can use cached list.
        if ($reset || !$this->_list)
        {
            // Get the list
            wimport('list.list');
            $xml = JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'lists'.DS.strtolower($this->getName()).'.xml';
            $this->_list = &WList::getInstance($xml);
        }

        return $this->_list;
    }

    /**
     * Method to validate the form data.
     *
     * @param   object  $data   The data to validate.
     * @return  boolean Array of filtered data if valid, false otherwise.
     * @since    1.1
     */
    function validate($data, $bindFilteredData = false)
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
            //@todo ?? isn't this enough?
            $form->bind($data);
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

    /**
     * Gets a JTable object that can be used to modify data related to this model.
     *
     * @param int $id PK value of teh recorde to load. Use null to get a clean table.
     * @param boolean $reload Force reload the table even if it is already loaded with the chosen record.
     * @return JTable
     */
    public function getTable($id = null, $reload = false)
    {
        if (!isset($this->_tableName))
        {
            throw new WException("WHD_E:NO TABLES ARE RELATED TO THIS MODEL");
        }

        $table = WFactory::getTable($this->_tableName);
        $pk = $table->getKeyName();
        if ($id) {
            if ($reload || $table->$pk != $id) {
                if (!$table->load($id)) {
                    throw new WException("WHD_E:COULD NOT LOAD DATA");
                }
            }
        } else {
            $table->reset();
            $table->$pk = 0;
        }

        return $table;
    }

    public function checkIn($id) {
        $this->getTable($id)->checkIn();
    }

    public function checkOut($id, $uid=0) {
        if (!$uid) {
            $uid = JFactory::getUser()->id;
        }
        $this->getTable($id)->checkOut($uid);
    }

    public function changeState($cid, $published) {
        // get the table and publish the identified terms
        $table = $this->getTable();
        $table->changeState($cid, ($published ? 1 : 0), JFactory::getUser()->id);
    }

    public function resetHits($id) {
        $table = $this->getTable();
        $table->resetHits($id);
    }
}
