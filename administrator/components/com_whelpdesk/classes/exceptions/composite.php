<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

/**
 * A composite exception is an exception that contains several parts, or rather
 * severla reasons for being raised in the first instance. For example if a
 * table check method fails it can throw a composite exception explaining all of
 * the reasons why each bit of invalid data is invalid.
 *
 * @author Administrator
 */
class WCompositeException extends WException {

    protected $messages = array();

    /**
     *
     * @param String $message The exception message
     */
    public function __construct() {
        $messages = func_get_args();
        for ($i = 0, $c = count($messages) ; $i < $c ; $i++) {
            if (is_array($messages[$i])) {
                $this->addMessages($messages[$i]);
            } else {
                $this->addMessage($messages[$i]);
            }
        }
        parent::__construct('WHD_EXCEPTION:COMPOSITE');
    }

    public function addMessage($message) {
        $this->messages[] = $message;
    }

    public function addMessages($messages) {
        $this->messages = array_merge($this->messages, $messages);
    }

    public function getMessages() {
        return $this->messages;
    }

}
