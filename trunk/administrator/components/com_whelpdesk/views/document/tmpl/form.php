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

<?php $document = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="javascript: populateAlias();"
      enctype="multipart/form-data">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="document.upload" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $document->id; ?>" />
    <input type="hidden" name="parent" value="<?php echo $document->parent; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <?php if (!$document->id) : ?>
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="upload">
                            <?php echo JText::_('WHD_DOC:UPLOAD DOCUMENT'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="input_box"
                               name="upload"
                               id="upload"
                               type="file"
                               size="80" /><br />
                        <?php echo JText::sprintf('WHD_DOC:MAXIMUM DOCUMENT SIZE IS %sMB', $this->getModel('maxFileSize')); ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php endif; ?>
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="name">
                            <?php echo JText::_('WHD_DOC:DOCUMENT NAME'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="name"
                               id="name"
                               size="40"
                               maxlength="500"
                               value="<?php echo $document->name; ?>"
                               onchange="populateAlias(false);" />
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <label for="alias">
                            <?php echo JText::_('WHD_DOC:DOCUMENT ALIAS'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text" name="alias" id="alias" size="34" maxlength="255" value="<?php echo $document->alias; ?>" />
                        <img id="rebuildAlias"
                             src="components/com_whelpdesk/assets/javascript/wall-disable.png"
                             alt="<?php echo JText::_('WHD:REBUILD ALIAS'); ?>"
                             title="<?php echo JText::_('WHD:REBUILD ALIAS'); ?>"
                             class="hasTip"
                             align="middle"
                             style="cursor: pointer;"
                             onclick="javascript: populateAlias(true);"
                             onmouseover="javascript: this.src = 'components/com_whelpdesk/assets/javascript/wall-build.gif'"
                             onmouseout="javascript: this.src = 'components/com_whelpdesk/assets/javascript/wall-disable.png'" />
                    </td>
                    <?php if ($document->id) : ?>
                    <td class="key">
                        <label for="filename">
                            <?php echo JText::_('FILENAME'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" 
                               type="text"
                               name="filename"
                               id="filename"
                               size="34"
                               maxlength="255"
                               value="<?php echo $document->filename; ?>" />
                    </td>
                    <?php endif; ?>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <?php echo $this->getModel('editor')->display('description',  $document->description, '100%', '200', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($document->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px; width: 100%;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED BY'); ?></strong>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&cid[]='.$this->getModel('creator')->get('id')); ?>"
                           target="__blank">
                            <?php echo $this->getModel('creator')->get('username'); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $document->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($document->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $document->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WEB ADDRESS'); ?></strong>
                    </td>
                    <td>
                        <input onclick="this.select();"
                               value="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=document.display&id='.$document->id); ?>"
                               readonly="readonly"
                               id="webAddress"
                               style="width: 100%;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('DOWNLOADS'); ?></strong>
                    </td>
                    <td>
                        <?php echo $document->hits;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('SIZE'); ?></strong>
                    </td>
                    <td>
                        <?php if ($document->bytes < 1024) : ?>
                        <?php echo $document->bytes; ?> B
                        <?php elseif (round(($document->bytes / 1048576), 1) < 0.1) : ?>
                        <?php echo round(($document->bytes / 1024), 1); ?> KB
                        <?php else : ?>
                        <?php echo round(($document->bytes / 1048576), 1); ?> MB
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php endif; ?>
        <?php //echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
