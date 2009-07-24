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
class WDatabaseConsistencyException extends WException {

    /**
     *
     * @param String $message The exception message
     */
    public function __construct() {
        $this->detail = func_get_args();

        $message = 'DATABASE CONSISTENCY EXCPETION';
        foreach ($this->detail as $row) {
            if (is_array($row)) {
                foreach ($row as $id) {
                    $message .= "\n".JText::sprintf("\tROW %s", $id);
                }
            } else {
                $message .= "\n".JText::sprintf('TABLE %s CONSISTENCY ERRORS ON ROWS:', $row);
            }
        }

        parent::__construct($message);
    }

}
