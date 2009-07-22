<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

/**
 * Parent class inclusion
 */
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . 'faqcategory.php');

abstract class FaqcategoriesWController extends FaqcategoryWController {

    public function __construct() {
        $this->setType('faqcategories');
    }

}
