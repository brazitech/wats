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
     * Executes the current command. The command is identified by the request
     * value task.
     */
    public function execute() {
        // prepare the command
        $command = explode('.', JRequest::getCmd('task', self::defaultCommand), 2);
        $entity  = $command[0];
        $usecase = $command[1];

        // clear the request command (task)
        JRequest::setVar('task', null);

        // get and execute the controller
        $controller = WController::getInstance($entity, $usecase);
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
}
?>