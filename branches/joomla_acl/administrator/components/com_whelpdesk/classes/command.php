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
    const defaultCommand = 'helpdesk.display.start';

    /**
     * Global instance of WCommand. We should never need more than one instance
     * of this class!
     */
    private static $instance = null;

    /**
     * Last type to be invoked
     *
     * @var String
     */
    private $type = 'helpdesk';

    /**
     * Last usecase to be invoked
     *
     * @var String
     */
    private $usecase = 'display';

    /**
     * Stage in the usecase
     *
     * @var string
     */
    private $stage = 'start';

    /**
     * Executes the current command. The command is identified by the request
     * value task.
     */
    public function execute() {
        // prepare the command
        $this->parseCommand();
        
        // clear the request command (task)
        JRequest::setVar('task', null);

        // get and execute the controller
        $controller = WController::getInstance($this->type, $this->usecase);
        WFactory::getOut()->log('Executing ' . $this->type . ', ' .
                                               $this->usecase . ', ' .
                                               $this->stage . ' usecase stage');
        $controller->execute($this->stage);
        WFactory::getOut()->log('Executed ' . $this->type . ', ' .
                                              $this->usecase . ', ' .
                                              $this->stage . ' usecase stage');

        // check for new command and keep going!
        if (JRequest::getCmd('task', false)) {
            $this->execute();
        }
    }

    private function parseCommand() {
        // get the command
        $command = explode('.', JRequest::getCmd('task', self::defaultCommand));
        $numberOfCommands = count($command);

        // check that we have a complete command
        if ($numberOfCommands >= 2) {
            $this->type  = $command[0];
            $this->usecase = $command[1];
            if ($numberOfCommands >= 3) {
                $this->stage   = $command[2];
            }
        } else {
            // command was nopt present or incomplete
            // try again with the default command
            JRequest::setVar('task', self::defaultCommand);
            $this->parseCommand();
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
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the name of the last usecase that was executed
     *
     * @return String
     */
    public function getUsecase() {
        return $this->usecase;
    }

    /**
     * Gets the stage in the last usecase that was executed
     *
     * @return String
     */
    public function getStage() {
        return $this->stage;
    }
}
?>