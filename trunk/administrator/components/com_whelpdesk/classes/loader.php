<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package wats
 */

function wimport($path) {
    if (!JLoader::import($path, dirname(__FILE__))) {
        throw new WException('IMPORT FAILED');
    }
}

// load the basic exception class WException
wimport('exception');

?>
