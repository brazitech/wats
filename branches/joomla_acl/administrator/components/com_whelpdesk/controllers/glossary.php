<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('application.model');

abstract class GlossaryWController extends WController {

    public function __construct() {
        $this->setType('glossary');
    }

    /**
     * Commits the $array array changes to the database
     *
     * @param array $array values to use to create new record
     * @return bool|int On fail returns boolean false, on success returns the PK value
     * @throws WCompositeException This exception is thrown when errors exist in the data
     */
    public function commit($id, $data) {
        // get the model
        $model = WModel::getInstance('glossary');

        try {
            // attempt to save the data
            $id = $model->save($id, $data);
        } catch (WCompositeException $e) {
            // data is not valid - output errors
            $id = false;
            JError::raiseWarning('500', JText::_('WHD_GLOSSARY:INVALID TERM DATA'));;
            foreach($e->getMessages() AS $message) {
                JError::raiseWarning('500', $message);
            }

            return false;
        }

        return $id;
    }
}

?>