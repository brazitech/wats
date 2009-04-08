<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

wimport('application.model');

class GlossaryListWController extends WController {

    public function __construct() {
        $this->setEntity('glossary');
    }

    /**
     * Displays the control panel
     */
    public function execute() {
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