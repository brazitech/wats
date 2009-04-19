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

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

class GlossaryResethitsWController extends GlossaryWController {

    /**
     * @todo
     */
    public function execute($stage) {
        // get the table
        $table = WFactory::getTable('glossary');

        // load the table data
        $id = WModel::getId();
        if (!$id) {
            JRequest::setVar('task', 'glossary.list.start');
            JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY UNKNOWN TERM'));
            return;
        }
        $table->load($id);

        // reset the hit vounter
        $table->resetHits();

        // return to the edit screen
        JRequest::setVar('task', 'glossary.edit.start');
        JError::raiseNotice('INPUT', JText::_('WHD GLOSSARY RESET HITS'));
    }
}

?>