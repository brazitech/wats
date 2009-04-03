<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.view');

abstract class WController {

    /**
     * 
     * @var WController[]
     */
    private static $instances = array();

    /**
     * Name of the entity that this controller handles. This must be set by
     * inheriting classes.
     *
     * @var String
     * @see WController::setEntity()
     */
    private $entity;

    /**
     * Does the business when executed. Sub class must override this method!
     */
    public function execute() {
        throw new WException('METHOD NOT IMPLEMENTED');
    }

    /**
     * Sets the name of the entity that this controller handles.
     *
     * @param String $name
     */
    protected function setEntity($name) {
        $this->entity = (string)$name;
    }

    /**
     * Gets the name of the entity that this controller handles.
     * 
     * @return String
     */
    public function getEntity() {
        return $this->entity;
    }

	/**
     * Outputs the view. This method assumes that we are using the MVC paradigm
     * to the letter and that we display by outputting the view layout contents.
     * This may not always be appropriate.
     */
    public function display() {
		// get the info we need to display the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
		$viewName =  JRequest::getCmd('view', 'display');

        // get the view
        $view = WView::getInstance($this->getEntity(), $viewName, $format = null);

        // output the view!
        echo $view->loadLayout();
	}

    /**
     *
     * @param <type> $entity
     * @param <type> $usecase
     * @return WController
     */
    public static function getInstance($entity, $usecase) {
        if (empty(self::$instances[$entity][$usecase])) {
            // get the controller class file
            $path = JPATH_COMPONENT . DS . 'controllers' 
                                    . DS . $entity
                                    . DS . $usecase . '.php';
            require_once($path);

            // create the controller
            $class = ucfirst($entity) . ucfirst($usecase) . 'WController';
            self::$instances[$entity][$usecase] = new $class();
        }

        // return the controller!
        return self::$instances[$entity][$usecase];
    }

}

?>