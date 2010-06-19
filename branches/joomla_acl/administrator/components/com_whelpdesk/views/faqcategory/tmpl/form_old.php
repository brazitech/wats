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

$document->addScriptDeclaration("function populateAlias(force) {
    if (document.getElementById('alias').value == '' || force == true) {
        var req = new Request({
            method: 'post',
            url: 'index.php',
            onRequest: function() {
                document.getElementById('alias').setStyle('background-image', 'url(components/com_whelpdesk/assets/javascript/ajax-loader-2.gif)');
            },
            data: {
                'option' : 'com_whelpdesk',
                'task'   : 'alias.build',
                'format' : 'json',
                'name'   : document.getElementById('name').value
            },
            onComplete: function(response) {
                response = eval('(' + response + ')');
                document.getElementById('alias').value = response.alias;
                document.getElementById('alias').setStyle('background-image', '');
                document.getElementById('rebuildAlias').src = 'components/com_whelpdesk/assets/javascript/wall-disable.png';
            }
        }).send();
    }
}");

?>

<?php WDocumentHelper::render(); ?>

<?php $knowledgeDomain = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $knowledgeDomain->id; ?>" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="name">
                            <?php echo JText::_('NAME'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="name"
                               id="name"
                               size="40"
                               maxlength="500"
                               value="<?php echo $knowledgeDomain->name; ?>"
                               onchange="populateAlias();" />
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <label for="alias">
                            <?php echo JText::_('ALIAS'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="alias"
                               id="alias"
                               size="34"
                               maxlength="255"
                               value="<?php echo $knowledgeDomain->alias; ?>" />
                        <img id="rebuildAlias"
                             src="components/com_whelpdesk/assets/javascript/wall-disable.png"
                             alt="<?php echo JText::_('Rebuild Alias'); ?>"
                             title="<?php echo JText::_('Rebuild Alias'); ?>"
                             class="hasTip"
                             align="middle"
                             style="cursor: pointer;"
                             onclick="javascript: populateAlias(true);"
                             onmouseover="javascript: this.src = 'components/com_whelpdesk/assets/javascript/wall-build.gif'"
                             onmouseout="javascript: this.src = 'components/com_whelpdesk/assets/javascript/wall-disable.png'" />
                    </td>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <?php echo $this->getModel('editor')->display('description',  $knowledgeDomain->description, '100%', '200', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($knowledgeDomain->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:REVISED'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledgeDomain->revised; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED BY'); ?></strong>
                    </td>
                    <td>
                        <?php echo JFactory::getUser($knowledgeDomain->created_by)->get('name'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $knowledgeDomain->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($knowledgeDomain->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $knowledgeDomain->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php endif; ?>
        <?php echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
