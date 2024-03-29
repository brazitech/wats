<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

/**
 * Description of exception
 *
 * @author Administrator
 */
class WException extends Exception {

    /**
     * Extra details about the exception that occured
     *
     * @var array
     */
    private $detail;

    /**
     *
     * @param String $message The exception message
     */
    public function __construct($message) {
        $this->detail = func_get_args();
        $message = call_user_func_array(array("JText", "sprintf"), $this->detail);
        array_shift($this->detail);
        parent::__construct($message);
    }

    /**
     * String representation of object
     * 
     * @return string
     */
    public function __toString() {
        return "Help-Desk-Exception: {$this->getMessage()}";
    }

    /**
     * Gets extra details about the exception. This information may already
     * be available within the message, but this will depend on the
     * translation file.
     * 
     * @return array Details of the exception
     */
    public function getDetail() {
        return $this->detail;
    }

    public static function getInstance($name) {
        wimport('exceptions.'.strtolower($name));

        $parameters = func_get_args();
        array_shift($parameters);

        return new call_user_func(array($name, '__construct'), $parameters);
    }

}
