<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

if ($this->getModel('replies')) :

ob_start();

$replies = $this->getModel('replies');
$rows = $replies->getRows();
$filters = $replies->getFilters();
$pagination = $replies->getPagination();

?>

<div class="requestReplies">
    <?php foreach ($rows as $row): ?>
    <fieldset class="requestReply adminform">
        <legend class="requestReplyHeader">
            <span class="requestReplyHeaderNameOfUser">
                <?php echo $replies->getColumn('name_of_user')->renderPlain($row); ?>
            </span>
            <span class="requestReplyHeaderCreated">
                <?php echo $replies->getColumn('created')->renderPlain($row); ?>
            </span>
        </legend>
        <div class="requestReplyDescription">
            <div class="requestReplyNumber">
                <?php echo JText::sprintf('WHD_R:REPLY %d:', ++$i); ?>
            </div>
            <?php echo $replies->getColumn('description')->renderPlain($row); ?>
        </div>
    </fieldset>
    <?php endforeach; ?>
</div>
<div class="requestReply">
    <fieldset class="adminform">
        <legend>
            <?php echo JText::_('WHD_R:REPLY'); ?>
        </legend>
        <?php
        $editor = JFactory::getEditor();
        echo $editor->display('reply_description', '', '100%', '250px', '', '', false, 'reply_description');
        ?>
    </fieldset>
</div>

<?php

$onAfterNormalFieldSets = ob_get_clean();

endif;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'generic'.DS.'tmpl'.DS.'form.php');
