<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Route handling class
 *
 * @static
 */
class WRoute {
    
	/**
	 * Translates an internal Joomla URL to a humanly readible URL.
	 *
	 * @access public
	 * @param 	string 	 $url 	Absolute or Relative URI to Joomla resource
	 * @param 	boolean  $xhtml Replace & by &amp; for xml compilance
	 * @param	int		 $ssl	Secure state for the resolved URI
	 * 		 1: Make URI secure using global secure site URI
	 * 		 0: Leave URI in the same secure state as it was passed to the function
	 * 		-1: Make URI unsecure using the global unsecure site URI
	 * @return The translated humanly readible URL
	 */
    public static function _($url, $xhtml = true, $ssl = null) {
        // use J! to create the initial route
        $route = JRoute::_($url, $xhtml, $ssl);
        $uri   = '';

        // if we don't have an HTTP or HTTPS scheme we need to add it
        if (strpos($route, 'http') == false) {
            // create the start of the URI
            $uri = JURI::getInstance()->toString(
                array(
                    'scheme',
                    'user',
                    'pass',
                    'host',
                    'port'
                )
            );

            // add directory separator if we need to
            if (!preg_match('~^/~', $route) && !preg_match('~/$~', $uri)) {
                $uri .= '/';
            }
        }

        // complete the full URI
		return $uri . $route;;
	}
    
    public static function site($url, $xhtml = true, $ssl = null) {
        ;
    }

    public static function administrator($url, $xhtml = true, $ssl = null) {
        ;
    }
}
