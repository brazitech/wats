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
class DocumentUploadWController extends DocumentWController {

    /**
     *
     * @var array
     */
    private $upload;

    public function __construct() {
        parent::__construct();
        $this->setUsecase('upload');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        $this->setType('documentcontainer');
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD DOCUMENT UPLOAD ACCESS DENIED');
            return;
        }
        $this->setType('document');

        // Check if uploads are allowed
        if ((boolean)ini_get('file_uploads') == false) {
            // @todo handle PHP uploads disabled
        }


        // get the table
        $table = WFactory::getTable('document');
        $table->parent = $this->getAccessTargetIdentifier();

        // check where in the usecase we are
        if ($stage == 'save' || $stage == 'apply') {

            // deal with the uploaded file
            try {
                $upload   = $this->getUpload();
                $payload  = $this->getPayload($upload);
                $mimeType = $this->getMIME_Type($upload);
            } catch (Exception $e) {
                // an exception occured.
                $upload = false;
            }

            // okay to attempt save/apply
            if ($upload) {
                // attempt to save
                if ($this->commit($payload, $upload['size'], $mimeType, $upload['name'])) {
                   JError::raiseNotice('INPUT', JText::_('WHD DOCUMENT SAVED'));
                   if ($stage == 'save') {
                       JRequest::setVar('id',   JRequest::getInt('parent', 1));
                       JRequest::setVar('task', 'documentcontainer.display.start');
                   } else {
                       JRequest::setVar('task', 'document.edit.start');
                       // @todo set request id
                   }

                   return;
                } else {
                    JError::raiseNotice('INPUT', JText::_('INVALID STUFF???'));;
                    foreach($table->getErrors() AS $error) {
                        JError::raiseNotice('INPUT', $error);
                    }
                }
            }

            // tidy up
            JFile::delete($upload['tmp_name']);
        }

        // get the model
        $model = WModel::getInstanceByName('document');

        // get the parents
        $parents = $model->getParents($table->parent);

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('document', 'form', $format);

        $view->addModel('document', $table, true);

        // add the fieldset to the model
        $view->addModel('fieldset', $table->getFieldset());
        $view->addModel('fieldset-data', $table);

        // add the parents to the view
        $view->addModel('parents', $parents);

        // add maximum file size directive
        $view->addModel('maxFileSize', $this->getMaximumUploadSizeInMegaBytes());

        // display the view!
        JRequest::setVar('view', 'form');
        $this->display();
    }

    private function getUpload() {
        if (JError::isError($this->upload)) {
            throw $this->upload;
        } elseif (!$this->upload) {
            $this->upload = JRequest::getVar('upload',
                                             null,
                                             'FILES',
                                             'ARRAY');

            // make sure we have a valid upload
            if (!is_array($this->upload)) {
                // handle no upload present
                $this->upload = JError::raiseNotice('INPUT', JText::_('PLEASE SELECT A FILE TO UPLOAD'));
                throw $this->upload;
                return;
            }
            if ($this->upload['error'] || $this->upload['size'] < 1) {
                // @todo handle upload error
                $this->upload = JError::raiseNotice('INPUT', JText::_('AN UPLOAD ERROR OCCURED PLEASE TRY AGAIN'));
                throw $this->upload;
                return;
            }

            if (!is_uploaded_file($this->upload['tmp_name'])) {
                // @todo handle potential malicous attack
                $this->upload = JError::raiseError('INPUT', JText::_('UPLOAD IS INVALID'));
                throw $this->upload;
                jexit();
            }

            // Prepare the temporary destination path
            $fileDestination  = JFactory::getConfig()->getValue('config.tmp_path')
                              . DS . JFile::getName($this->upload['tmp_name']);

            // Move uploaded file
            if (!JFile::upload($this->upload['tmp_name'], $fileDestination)) {
                $this->upload = JError::raiseError('INPUT', JText::_('COULD NOT COPY FILE TO TEMPORARY LOCATION'));
                throw $this->upload;
            }
            // change the path to the file to the Joomla! temporary file
            $this->upload['tmp_name'] = $fileDestination;
        }

        return $this->upload;
    }

    private function getPayload($upload) {
        // get the payload
        return JFile::read($upload['tmp_name']);
    }

    private function getMIME_Type($upload) {
        $mimeType = $upload['type'];

        if (function_exists('finfo_file')) {
            // determine MIME type using PECL Fileinfo
            $finfo    = finfo_open(FILEINFO_MIME);
            $mimeType = finfo_file($finfo, $upload['tmp_name']);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            // determine MIME type using PHP mime_content_type() function
            $mimeType = mime_content_type($upload['tmp_name']);
        }

        return $mimeType;
    }

    public function commit($payload, $size, $mimeType, $filename) {
        // values to use to create new record
        $post = JRequest::get('POST');

        // do not provide an ID
        unset($post['id']);

        // make sure parent is an integer
        $post['parent'] = intval($post['parent']);

        // add the file data
        $post['payload']   = $payload;
        $post['bytes']     = $size;
        $post['mime_type'] = $mimeType;
        $post['filename']  = $filename;

        return parent::commit($post);
    }

    /**
     *
     * @return int
     * @todo
     */
    protected function getAccessTargetIdentifier() {
        return JRequest::getInt('parent', 1);
    }

    private function getMaximumUploadSizeInBytes() {
        $uploadSize = trim(ini_get('upload_max_filesize'));
        $last = strtolower($uploadSize[strlen($uploadSize)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $uploadSize *= 1024;
            case 'm':
                $uploadSize *= 1024;
            case 'k':
                $uploadSize *= 1024;
        }

        return $uploadSize;
    }

    private function getMaximumUploadSizeInMegaBytes() {
        $bytes = $this->getMaximumUploadSizeInBytes();
        return $bytes / 1048576;
    }
}

?>