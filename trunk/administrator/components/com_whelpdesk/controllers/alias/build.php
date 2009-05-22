<?php
/**
 * @version		$Id$
 * @package		whelpdesk
 * @package		classes
 * @license		GNU/GPL
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('helper.alias');

class AliasBuildWController extends WController {

    public function  __construct() {
        $this->setType('alias');
        $this->setUsecase('edit');
    }

    public function execute($stage) {
        $name = JRequest::getVar('name');
        $alias = WAliasHelper::buildAlias($name);
        
        // get the view
        $document =& JFactory::getDocument();
        $format   =  strtolower($document->getType());
        $view = WView::getInstance('alias', 'build', $format);

        // add data to the view
        $view->addModel('alias', $alias, true);
        $view->addModel('name', $name);

        // display the view!
        JRequest::setVar('view', 'build');
        $this->display();
    }

}

?>