<?php
/**
 * @version $Id: form.php 122 2009-05-29 14:49:37Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.tooltip');

$document = JFactory::getDocument();

$document->addScript('components/com_whelpdesk/assets/javascript/php.js/urlencode.js');
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
                response = eval('(' + trim(response) + ')');
                document.getElementById('alias').value = response.alias;
                document.getElementById('alias').setStyle('background-image', '');
                document.getElementById('rebuildAlias').src = 'components/com_whelpdesk/assets/javascript/wall-disable.png';
            }
        }).send();
    }
}");

?>

<?php WDocumentHelper::render(); ?>

<?php $documentcontainer = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="glossary.create" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $documentcontainer->id; ?>" />
    <input type="hidden" name="parent" value="<?php echo $documentcontainer->parent; ?>" />
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
                               value="<?php echo $documentcontainer->name; ?>"
                               onchange="populateAlias(false);" />
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <label for="alias">
                            <?php echo JText::_('Alias'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text" name="alias" id="alias" size="34" maxlength="255" value="<?php echo $documentcontainer->alias; ?>" />
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
                        <?php echo $this->getModel('editor')->display('description',  $documentcontainer->description, '100%', '200', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($documentcontainer->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('CONTAINER ID'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo $documentcontainer->id; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('State'); ?></strong>
                    </td>
                    <td>
                        <?php echo $documentcontainer->published ? JText::_('Published') : JText::_('UnPublished');?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('Hits'); ?></strong>
                    </td>
                    <td>
                        <?php echo $documentcontainer->hits;?>
                        <?php if ($this->getModel('canResetHits') && $documentcontainer->hits) : ?>
                        <input name="resetHits"
                               type="button"
                               class="button"
                               value="<?php echo JText::_('Reset'); ?>"
                               onclick="javascript: submitbutton('glossary.resethits');" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                <td>
                <strong><?php echo JText::_('Created'); ?></strong>
                </td>
                <td>
                <?php echo JHtml::_('date',  $documentcontainer->created,  JText::_('DATE_FORMAT_LC2')); ?>
                </td>
                </tr>
                <?php if ($documentcontainer->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('Modified'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $documentcontainer->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php endif; ?>
        <?php echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
