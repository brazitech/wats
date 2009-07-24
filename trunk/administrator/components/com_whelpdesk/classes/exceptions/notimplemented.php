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
class WNotImplementedException extends WException {

    /**
     *
     * @param String $message The exception message
     */
    function __construct() {
        parent::__construct();

        // build message
        $trace = $this->getTrace();
        $this->message = JText::sprintf('%s NOT IMPLEMENTED', $trace[0]['function']);
    }

}
?>
