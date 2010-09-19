<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

jimport('joomla.filesystem.file');
wimport('application.view');

abstract class WController {

    /**
     * 
     * @var WController[]
     */
    private static $instances = array();

    /**
     * Name of the type that this controller handles. This must be set by
     * inheriting classes.
     *
     * @var String
     * @see WController::setType()
     */
    private $type;

    /**
     * Name of the usevase that this controller handles. This must be set by
     * inheriting classes.
     *
     * @var String
     * @see WController::setUsecase()
     */
    private $defaultView;

    /**
     * Does the business when executed. Sub classes must override this method!
     */
    public function execute($stage) {
        // check for access
        if (!$this->hasAccess()) {
            // try the next control
            $accessSession = WFactory::getAccessSession();
            $controlPath = $accessSession->getControlPath();
            if (count($controlPath) > 1) {
                $nextControl = $controlPath[1];
                JRequest::setVar('task', $nextControl['type'] . '.' . $nextControl['identifier']);
            }

            // throw an exception, this must be handled for the next control to
            // be given a chance.
            throw new WException('NEXT CONTROL');
        }
    }

    /**
     * Sets the name of the entity that this controller handles.
     *
     * @param String $name
     */
    protected function setType($name) {
        $this->type = (string)$name;
    }

    /**
     * Gets the name of the entity that this controller handles.
     * 
     * @return String
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the name of the usecase that this controller handles.
     *
     * @param String $name
     */
    protected function setDefaultView($name) {
        $this->defaultView = (string)$name;
    }

    /**
     * Gets the name of the entity that this controller handles.
     *
     * @return String
     */
    public function getDefaultView() {
        return $this->defaultView;
    }

	/**
     * Outputs the view. This method assumes that we are using the MVC paradigm
     * to the letter and that we display by outputting the view layout contents.
     * This may not always be appropriate.
     */
    public function display() {
		// get the info we need to display the view
        $command  = WCommand::getInstance();
        $document =& JFactory::getDocument();
		$format   =  strtolower($document->getType());
		$viewName =  JRequest::getCmd('view', $this->getDefaultView());

        // get the view
        $view = WView::getInstance($this->getType(), $viewName, $format);

        // output the view!
        $view->render();
	}

    /**
     * Gets an instance of a concrete controller
     *
     * @param string $entity
     * @param string $usecase
     * @return WController
     * @throws WException
     */
    public static function getInstance($entity, $usecase) {
        if (empty(self::$instances[$entity][$usecase])) {
            // get the controller class file
            $path = JPATH_COMPONENT . DS . 'controllers' 
                                    . DS . $entity
                                    . DS . $usecase . '.php';
            if (!JFile::exists($path)) {
                throw new WException('WHD_E:UNKNOWN CONTROLLER (%s, %s)', $entity, $usecase);
            }
            require_once($path);

            // create the controller
            $class = ucfirst($entity) . ucfirst($usecase) . 'WController';
            self::$instances[$entity][$usecase] = new $class();
        }

        // return the controller!
        return self::$instances[$entity][$usecase];
    }

    /**
     * Checks for access, if no access, method throws an excpetion. It is up to
     * the caller to decide what to do with it!
     *
     * @param string $targetIdentifier
     * @param string $targetType
     * @param string $control
     * @param string $controlType
     * @throws WException
     */
    protected function hasAccess($targetIdentifier = null, $targetType = null,
                                 $control = null, $controlType = null) {
        return true;
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return $this->getType();
    }

    /**
     * Commits the data to the database
     *
     * @param int|string $id Primary key, usually an integer
     * @param object|array $data Values to commit
     * @param WModel $model Model to use to commit the changes described in $data
     * @return bool|int On fail returns boolean false, on success returns the PK value
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    protected function commit($id, $data, WModel $model) {

        try {
            // attempt to save the data
            $id = $model->save($id, $data);
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_FORM:INVALID'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return $id;
    }

}

?>