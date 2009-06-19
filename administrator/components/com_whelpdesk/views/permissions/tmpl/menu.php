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

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"           value="com_whelpdesk" />
    <input type="hidden" name="task"             value=""/>
    <input type="hidden" name="targetIdentifier" value="<?php echo $this->getModel('targetIdentifier'); ?>" />
    <input type="hidden" name="targetType"       value="<?php echo $this->getModel('targetType'); ?>" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode($this->getModel('targetIdentifierAlias')); ?>" />
    <input type="hidden" name="returnURI"        value="<?php echo base64_encode($this->getModel('returnURI')); ?>" />
    <?php echo JHTML::_('form.token'); ?>

<table width="100%">
    <thead>
        <th>
            Edit Perms...
        </th>
        <th>
            View Perms...
        </th>
    </thead>
    <tbody>
        <tr>
            <td width="50%" align="center">
                <img src="components/com_whelpdesk/assets/icons/ksystemlog.png"
                     alt="Edit"
                     onclick="javascript: submitform('permissions.edit.selectRequestNodeType');"
                     style="cursor: pointer;"/>
            </td>
            <td align="center">
                <img src="components/com_whelpdesk/assets/icons/Login Manager.png"
                     alt="Users"
                     onclick="javascript: submitform('permissions.list');"
                     style="cursor: pointer;"/>
            </td>
        </tr>
    </tbody>
</table>

</form>
