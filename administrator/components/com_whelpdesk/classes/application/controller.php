<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

jimport('joomla.application.component.controller');

abstract class WController extends JController {

    /**
     * 
     * @var WController[]
     */
    private static $instances = array();

    /**
     * Does the business when executed. Sub class must override this method!
     */
    public function execute() {
        throw new WException('METHOD NOT IMPLEMENTED');
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