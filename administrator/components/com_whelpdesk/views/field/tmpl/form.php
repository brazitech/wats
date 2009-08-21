<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.tooltip');

$document = JFactory::getDocument();

?>

<?php WDocumentHelper::render(); ?>

<?php $field = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm" >

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <?php if ($field->version != null) : ?>
    <input type="hidden" name="id"     value="<?php echo $field->group; ?>.<?php echo $field->name; ?>" />
    <?php else : ?>
    <input type="hidden" name="group" value="<?php echo $field->group; ?>" />
    <input type="hidden" name="type"  value="<?php echo $field->type; ?>" />
    <?php endif; ?>
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="name">
                            <?php echo JText::_('WHD_CD:FIELD'); ?>
                        </label>
                    </td>
                    <td style="font-family: courier new, courier;">
                        `field_<?php echo $field->groupName; ?>_<?php echo ($field->version !== null) ? $field->name : '<input class="inputbox"
                               type="text"
                               name="name"
                               id="name"
                               size="10"
                               maxlength="20"
                               value="'. $field->name .'"
                               style="font-family: courier new, courier;" />'; ?>`
                    </td>
                    <td class="key">
                        <label for="label">
                            <?php echo JText::_('WHD_CD:FIELD LABEL'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="label"
                               id="label"
                               size="40"
                               maxlength="500"
                               value="<?php echo $field->label; ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <label for="default">
                            <?php echo JText::_('WHD_CD:FIELD DEFAULT'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="default"
                               id="default"
                               size="40"
                               maxlength="500"
                               value="<?php echo $field->default; ?>" />
                    </td>
                    <td class="key">
                        <label for="list">
                            <?php echo JText::_('WHD_CD:FIELD LIST'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="radio"
                               name="list"
                               id="listYes"
                               value="1"
                               <?php echo ($field->list) ? 'checked' : ''; ?> />
                        <?php echo JText::_('YES'); ?>
                        <input class="inputbox"
                               type="radio"
                               name="list"
                               id="listNo"
                               value="0"
                               <?php echo (!$field->list) ? 'checked' : ''; ?> />
                        <?php echo JText::_('NO'); ?>
                    </td>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <?php echo $this->getModel('editor')->display('description', $field->description, '100%', '100', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
            <?php echo $this->addModel('jparameter', $field->params); ?>
            <?php echo $this->loadLayout('jparameter_adminform'); ?>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($field->name) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $field->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($field->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $field->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($field->version) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:REVISION'); ?></strong>
                    </td>
                    <td>
                        <?php echo $field->version; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php endif; ?>
        <?php //echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
