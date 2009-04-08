<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

wimport('application.controller');

/**
 * @todo document class
 */
final class WCommand {

    /**
     * The command to execute by default
     */
    const defaultCommand = 'controlpanel.display';

    /**
     * Global instance of WCommand. We should never need more than one instance
     * of this class!
     */
    private static $instance = null;

    /**
     * Last entity to be invoked
     *
     * @var String
     */
    private $entity;

    /**
     * Last usecase to be invoked
     *
     * @var String
     */
    private $usecase;

    /**
     * Executes the current command. The command is identified by the request
     * value task.
     */
    public function execute() {
        // prepare the command
        $command = explode('.', JRequest::getCmd('task', self::defaultCommand), 2);
        $this->entity  = $command[0];
        $this->usecase = $command[1];

        // clear the request command (task)
        JRequest::setVar('task', null);

        // get and execute the controller
        $controller = WController::getInstance($this->entity, $this->usecase);
        $controller->execute();

        // check for new command and keep going!
        if (JRequest::getCmd('task', false)) {
            $this->execute();
        }
    }

    /**
     *
     * @return WCommand
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
    public function getEntity() {
        return $this->entity;
    }

    /**
     * Gets the name of the last usecase that was executed
     *
     * @return String
     */
    public function getUsecase() {
        return $this->usecase;
    }
}
?>