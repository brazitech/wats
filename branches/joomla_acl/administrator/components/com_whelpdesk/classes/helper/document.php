<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

/**
 * Interface that all implementing classes must implement. This is used to
 * create a generic class interface for all WDocumentHelper implementations.
 */
interface WDocumentHelperInterface {
    public static function title($title, $icon = 'whelpdesk');
    public function subtitle($subtitle=null);
    public function description($description);
}

// import the current WToolbarHelper
if (JFactory::getApplication()->isSite()) {
    wimport('helper.document.site');
} else {
    wimport('helper.document.admin');
}
