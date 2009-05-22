<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

?>

<div>
    <div>
        <?php echo $this->getModel(); ?>
    </div>
    <div>
        <?php echo $this->getModel('name'); ?>
    </div>
</div>