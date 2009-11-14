<?php
/**
 * @version     $Id$
 * @package     whelpdesk
 * @subpackage  classes
 * @license     GNU/GPL
 */

class WAliasHelper {

    static function buildAlias($name, $sympathetic = true, $replacement = '') {
        // make sure we are dealing with a UTF-8 string
        $currentEncoding = mb_detect_encoding($name) ;
        if($currentEncoding != "UTF-8" && !mb_check_encoding($name,"UTF-8")) {
            $name = utf8_encode($name); 
        }
        
        // replace HTML entities with their actual UTF-8 character representation
        $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
        
        // check for non compliant characters
        // # < > [ ] | { } . :
        // and check for illegal start of name
        // ./ ../
        $notAllowed      = '~[\#\<\>\[\]\|\{\}\.\:]~';
        $notAllowedStart = '~^\.\.?\/~';
        if ($sympathetic) {
            // replace bad characters
            $name = preg_replace($notAllowed,      $replacement, $name);
            $name = preg_replace($notAllowedStart, $replacement, $name);
        } elseif (preg_match($notAllowed, $name) || preg_match($notAllowedStart, $name)) {
            // contains bad characters and we are not ina good mood! bad method caller bad!
            throw new WExcpetion('DISALLOWED CHARACTERS DETECTED IN TITLE');
        }
        
        // replace question marks with URL percent-encodced equivalent
        $name = preg_replace('~\?~', '%3F', $name);
        
        // replace plus signs with URL percent-encodced equivalent
        $name = preg_replace('~\+~', '%2B', $name);
        
        
        // replace white-space with underscores
        $name = preg_replace('~\s~', '_', $name);
        
        return $name;
    }

    /**
     * Determines if an alias is well formed
     *
     * @param string $alias
     */
    static function isValid($alias) {
        if (!is_string($alias)) {
            throw new Exception('WHD ALIAS MUST BE A STRING');
        }

        if (!strlen($alias)) {
            // alias must contain at least one character
            return false;
        }

        // compare against a true alias - if they are equal the alias is valid
        return (self::buildAlias($alias) == $alias);
    }

}
