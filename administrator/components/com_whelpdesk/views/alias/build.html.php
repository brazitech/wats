<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();


class AliasHTMLWView extends WView {

    /**
	 * Constructor
	 */
	public function __construct() {
        // set the layout
        $this->setLayout('build');

        // let the parent do what it does...
        parent::__construct();
	}

}

?>
