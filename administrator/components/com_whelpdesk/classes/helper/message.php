<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

/**
 * Interface that all implementing classes must implement. This is used to 
 * create a generic class interface for all WToolbarHelper implementations.
 */
abstract class WMessageHelper {

    public static function message($message) {
        JFactory::getApplication()->enqueueMessage($message);
    }

}
