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
interface WToolbarHelperInterface {
    
}

// import the current WToolbarHelper
if (JFactory::getApplication()->isSite()) {
    wimport('helper.toolbar.site');
} else {
    wimport('helper.toolbar.admin');
}
