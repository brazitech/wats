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
 * Get parent class
 */
require_once(JPATH_COMPONENT . DS . 'views' . DS . 'knowledgedomain' . DS . 'form.html.php');

/**
 * Too similar to the edit view for knowledge domain to make it worth while
 * defining an entirely new class
 */
class KnowledgedomainsHTMLWView extends KnowledgedomainHTMLWView {

}

?>
