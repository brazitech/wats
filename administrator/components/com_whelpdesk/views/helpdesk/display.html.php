<?php

class HelpdeskHTMLWView extends WView {

	public function render() {
        // populate the toolbar
        $this->toolbar();

        // continue!
        parent::render();
    }

    /**
     * Setup the toolbar
     */
    private function toolbar() {
        WToolBarHelper::permissions();
        WToolbarHelper::divider();
        WToolbarHelper::help('knowledgedomain-list');
    }
}

?>
