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

class GlossaryCopyWController extends GlossaryWController {

    public function  __construct() {
        parent::__construct();
        // this is a bit odd - in that the usecase is actually 'copy'. However
        // this would lead to unnecesary complication of the permissions, so we
        // may as well lump these in together
        $this->setUsecase('edit');
    }

    /**
     * @todo check token
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD_GLOSSARY:COPY ACCESS DENIED');
            return;
        }

        // before saving or applying the new term, make sure the token is valid
        shouldHaveToken();

        // get the model and the term
        $id = WModel::getId();
        $model = WModel::getInstanceByName('glossary');
        $term = $model->getTerm($id);

        // make sure the source data exists
        if(!$term) {
            JError::raiseError(JText::_('WHD_GLOSSARY:UNKNOWN TERM'));
        }

        // make sure the record isn't checked out by anyone else
        if (!$term->isCheckedOut(JFactory::getUser()->get('id'))) {
            // stop editing/checkin the record
            $model->checkIn($id);
        }

        // now we can create the copy!
        JRequest::setVar('task', 'glossary.create.apply');
    }

    /**
     * Overrides GlossaryWController::commit() and automatically pulls in the
     * new data from the request (this must be POST data). This takes account of
     * state change permissions such that the state cannot be changed through
     * this method unless the user has the necessary permissions.
     *
     * @return bool|int On fail returns boolean false, on success returns the PK value
     */
    public function commit() {
        // values to use to create new record
        $post = JRequest::get('POST');
        
        // check if we should allow state change
        $canChangeState = false;
        try {
            $canChangeState = WFactory::getAccessSession()->hasAccess('user', JFactory::getUser()->get('id'),
                                                        'glossary', 'glossary',
                                                        'glossary', 'state');
        } catch (Exception $e) {
            $canChangeState = false;
        }
        if (!$canChangeState) {
            unset($post['published']);
        }

        // commit the changes
        return parent::commit($post);
    }
}

?>