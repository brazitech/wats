<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package wats-plugins
 * @subpackage mailnotification
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define("WPATH_PLUGIN_MAILNOTIFICATION_TEMPLATES", WPATH_PLUGINS . DS . "mailnotification");

jimport("joomla.filesystem.file");
jimport("joomla.filesystem.file");

class WEmailView {

    var $_tmplPath = "";

    /**
     * Assigns object properties, array keys, or a key and value
     * 
     * @access public
     * @return bool True on success, false on failure.
     */
    function assign()
    {
        // get the arguments; there may be 1 or 2.
        $arg0 = @func_get_arg(0);
        $arg1 = @func_get_arg(1);

        // assign by object
        if (is_object($arg0))
        {
            // assign public properties
            foreach (get_object_vars($arg0) as $key => $val)
            {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            return true;
        }
        
        // assign by associative array
        if (is_array($arg0))
        {
            foreach ($arg0 as $key => $val)
            {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            return true;
        }
        // assign by string name and mixed value.
 
        // we use array_key_exists() instead of isset() becuase isset()
        // fails if the value is set to null.
        if (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1)
        {
            $this->$arg0 = $arg1;
            return true;
        }
 
        // $arg0 was not object, array, or string.
        return false;
    }

    /**
     * Assigns an object or array by reference
     *
     * @access public
     * @param string $key The name for the reference in the view.
     * @param mixed &$val The referenced variable.
     * @return bool True on success, false on failure.
     */ 
    function assignRef($key, &$val)
    {
        if (is_string($key) && substr($key, 0, 1) != '_')
        {
            $this->$key =& $val;
            return true;
        }
 
        return false;
    }
    
    /**
     * Renders the email and returns the rendered email as a string
     *
     * @access public
     * @param $tmpl Name of template to load
     * @param $type Type of template to load, "html" or "text"
     * @return string
     */
    function render($tmpl, $type) {
        // determine the path
        $tmpl = JFile::makeSafe($tmpl);
        $type = ($type == "html") ? "html" : "text";
        $this->_tmplPath = WPATH_PLUGIN_MAILNOTIFICATION_TEMPLATES . DS . $type . DS . $tmpl . ".tmpl";
        
        // start buffering
        if (!ob_start()) {
            return JError::raiseWarning(500, JText::_("FAILED TO OPEN EMAIL CONTENT BUFFER"));
        }
        
        // get rid of $tmpl, $type so as they do not interfere with the template
        unset($tmpl, $type);
        
        // render the template
        include($this->_tmplPath);
        
        // get the buffer contents and stop buffering
        $emailBody = ob_get_contents();
        if (!ob_end_clean()) {
            return JError::raiseWarning(500, JText::_("FAILED TO CLOSE EMAIL CONTENT BUFFER"));
        }
        
        return $emailBody;
    }

}

?>