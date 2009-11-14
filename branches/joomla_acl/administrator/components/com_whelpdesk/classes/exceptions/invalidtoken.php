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
class WInvalidTokenException extends WException {

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
    public function __construct($message = 'INVALID TOKEN') {
        parent::__construct($message);
    }

}

/**
 * Helper function that checks if the token is valid, if it is not it throws a
 * new WInvalidTokenException.
 *
 * @throws WInvalidTokenException
 */
function shouldHaveToken() {
    if (!JRequest::checkToken()) {
        WFactory::getOut()->log('Token is not valid');
        throw new WInvalidTokenException();
    }

    WFactory::getOut()->log('Token is valid');
}
