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
            Users
        </th>
        <th>
            User Groups
        </th>
    </thead>
    <tbody>
        <tr>
            <td width="50%" align="center">
                <img src="components/com_whelpdesk/assets/icons/128-userconfig.png" 
                     alt="Users"
                     onclick="javascript: submitform('permissions.edit.findUserRequestNode');"
                     style="cursor: pointer;"/>
            </td>
            <td align="center">
                <img src="components/com_whelpdesk/assets/icons/128-package_edutainment.png" 
                     alt="User Groups"
                     onclick="javascript: submitform('permissions.edit.findGroupRequestNode');"
                     style="cursor: pointer;"/>
            </td>
        </tr>
    </tbody>
</table>

</form>
