<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'document.php');

/**
 * 
 */
class DocumentDownloadWController extends DocumentWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('download');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT DOWNLOAD ACCESS DENIED');
            return;
        }

        // get the table
        $table = WFactory::getTable('document');
        $table->load($this->getAccessTargetIdentifier());

        // check the document is RAW
        $document =& JFactory::getDocument();
        if (!is_a($document, 'JDocumentRAW')) {
            $document = JDocument::getInstance('raw');
        }

        // output the document
        $document->setMimeEncoding($table->mime_type);
        JResponse::setHeader('Content-Disposition', 'attachment; filename="'.$table->filename).'"';
        echo $table->payload;

        // update download counter
        $table->hit();
    }

    /**
     *
     * @return String
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return WModel::getId();
    }
}

?>