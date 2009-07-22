<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

class DocumentTreeNode extends TreeNode {

    /**
     * Method which is called when a document is deleted
     * 
     * @param int $id
     */
    public function delete($id) {
        $table = WFactory::getTable('document');
        $table->delete($id);
    }

}
