<?php
/**
 * @version $Id: glossary.php 158 2009-07-30 14:12:39Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

abstract class FieldWController extends WController {

    public function __construct() {
        $this->setType('field');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return bool|int On fail returns boolean false, on success returns the PK value
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    public function commit($group, $name, $data, $isNew=false) {
        // get the model
        $model = WModel::getInstanceByName('field');

        try {
            // attempt to save the data
            $id = $model->save($group, $name, $data, $isNew);
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_CD:INVALID FIELD DATA'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return $id;
    }
}

?>