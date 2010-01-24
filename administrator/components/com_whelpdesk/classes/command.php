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
    private static $_instance = null;

    /**
     * Last type to be invoked
     *
     * @var String
     */
    private $_type = 'helpdesk';

    /**
     * Last usecase to be invoked
     *
     * @var String
     */
    private $_usecase = 'display';

    /**
     * Stage in the usecase
     *
     * @var string
     */
    private $_stage = 'start';

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
        $controller = WController::getInstance($this->_type, $this->_usecase);
        WFactory::getOut()->log('Executing ' . $this->_type . ', ' .
                                               $this->_usecase . ', ' .
                                               $this->_stage . ' usecase stage');
        $controller->execute($this->_stage);
        WFactory::getOut()->log('Executed ' . $this->_type . ', ' .
                                              $this->_usecase . ', ' .
                                              $this->_stage . ' usecase stage');

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
            $this->_type  = $command[0];
            $this->_usecase = $command[1];
            if ($numberOfCommands >= 3) {
                $this->_stage   = $command[2];
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
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        // all done, send it home!
        return self::$_instance;
    }

    /**
     * Gets the name of the last entity on which a usecase was executed
     *
     * @return String
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Gets the name of the last usecase that was executed
     *
     * @return String
     */
    public function getUsecase() {
        return $this->_usecase;
    }

    /**
     * Gets the stage in the last usecase that was executed
     *
     * @return String
     */
    public function getStage() {
        return $this->_stage;
    }

    public function  __toString()
    {
        return $this->_type.'.'.$this->_usecase.'.'.$this->_stage;
    }
}
?>