<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

/**
 * @todo document class
 */
final class WOut {
    /**
     *
     * Global instance of WCommand. We should never need more than one instance
     * of this class!
     */
    private static $instance = null;

    /**
     * Last entity to be invoked
     *
     * @var String
     */
    private $logs = array();

    private $enabled = false;

    public function  __construct() {
        $this->enabled = WFactory::getConfig()->get('debug');
        $this->enabled = true;
        $this->log('Output log opened');
    }

    /**
     * Adds a line to the log
     */
    public function log($message, $highlight=false) {
        if ($this->enabled) {
            $this->logs[] = array($message, $highlight);
        }
    }

    /**
     *
     * @return WOut
     */
    public static function getInstance() {
        // create instance if it does not exist
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // all done, send it home!
        return self::$instance;
    }

    /**
     * Gets the name of the last entity on which a usecase was executed
     *
     * @return String
     */
    public function getLogs() {
        return $this->logs;
    }
}
?>