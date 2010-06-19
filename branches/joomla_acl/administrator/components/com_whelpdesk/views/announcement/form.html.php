<?php
/**
 * @version $Id: form.html.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class AnnouncementHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('form');

        // let the parent do what it does...
        parent::__construct();
	}

    public function render() {
        // populate the toolbar
        $this->toolbar();

        // add metadata to the document
        $this->document();

        // add the editor
        $this->addModel('editor', JFactory::getEditor());

        // disable the menu
        JRequest::setVar('hidemainmenu', 1);

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        $command = WFactory::getCommand();
        WToolBarHelper::save('announcement.'.$command->getUsecase().'.save');
        WToolBarHelper::apply('announcement.'.$command->getUsecase().'.apply');
        WToolBarHelper::cancel('announcement.list.start');
        WToolbarHelper::divider();
        WToolbarHelper::help('announcement-form');
    }

    private function document() {
        $announcement = $this->getModel();
        if ($announcement->getValue('id'))
        {
            WDocumentHelper::subtitle(JText::sprintf('WHD_A:EDITING ANNOUNCEMENT %s', $announcement->getValue('name')));
        }
        else
        {
            WDocumentHelper::subtitle(JText::_('WHD_A:NEW ANNOUNCEMENT'));
        }
    }

}

?>
