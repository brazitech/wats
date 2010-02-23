<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of requeststatus
 *
 * @author Administrator
 */
class RequestStatus {

    public static $OPEN = 1;
    public static $CLOSED = 2;
    public static $REOPENED = 3;
    public static $ARCHIVED = 4;

    /**
     *
     * @param int $from
     * @param int $to
     * @return boolean
     */
    public static function canChangeTo($from, $to)
    {
        switch ($from)
        {
            case self::$OPEN:
            case self::$REOPENED:
                // can be closed
                return ($to == self::$CLOSED);
            case self::$CLOSED:
                // can be reopened or archived
                return ($to == self::$REOPENED || $to == self::$ARCHIVED);
            case self::$ARCHIVED:
                // cannot be changed
                return false;
        }

        // this should never happen
        throw new WException("INVALID REQUEST STATUS");
    }

    /**
     * Gets a textual equivalent of the request status.
     *
     * @param int $requestStatus
     * @return String
     */
    public static function toText($requestStatus)
    {
        if ($requestStatus == null)
        {
             return JText::_('WHD_R:STATUS:NONE');
        }

        $states = get_class_vars('RequestStatus');

        foreach ($states as $id => $value)
        {
            if ($requestStatus == $value)
            {
                return JText::_('WHD_R:STATUS:'.$id);
            }
        }

        // this should never happen
        throw new WException("INVALID REQUEST STATUS");
    }

}

