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
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'glossary.php');

/**
 * 
 */
class GlossaryListWController extends GlossaryWController {

    public function __construct() {
        parent::__construct();
        $this->setUsecase('list');
    }

    /**
     * Displays the control panel
     */
    public function execute($stage) {
        try {
            parent::execute($stage);
        } catch (Exception $e) {
            // uh oh, access denied... let's give the next controller a whirl!
            JError::raiseWarning('401', 'WHD GLOSSARY LIST ACCESS DENIED');
            return;
        }

        // get the model
        $model = WModel::getInstance('glossary');

        // get the list data
        $list = $model->getList();

        // get the view
        $document =& JFactory::getDocument();
		$format =  strtolower($document->getType());
        $view = WView::getInstance('glossary', 'list', $format);

        // add the default model to the view
        $view->addModel('terms', $list, true);

        // add the total number of terms to the view
        $view->addModel('total', $model->getTotal());

        // display the view!
        $this->display();
    }
}

?>