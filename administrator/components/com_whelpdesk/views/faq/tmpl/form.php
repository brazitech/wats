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
                'name'   : document.getElementById('question').value
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

<?php $faq = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $faq->id; ?>" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="question">
                            <?php echo JText::_('QUESTION'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="question"
                               id="question"
                               size="40"
                               maxlength="500"
                               value="<?php echo $faq->question; ?>"
                               onchange="populateAlias(false);" />
                    </td>
                    <?php if (!$faq->id || $this->getModel('canChangeState')): ?>
                    <td class="key">
                        <label>
                            <?php echo JText::_('Published'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="inputbox" name="published" id="published">
                            <option <?php echo ($faq->published == 1) ? 'selected="selected"' : ''; ?> value="1">Published</option>
                            <option <?php echo ($faq->published == 0) ? 'selected="selected"' : ''; ?> value="0">Unpublished</option>
                        </select>
                    </td>
                    <?php endif; ?>
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
                               value="<?php echo $faq->alias; ?>" />
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
                    <?php if (!$faq->id): ?>
                    <td class="key">
                        <label for="alias">
                            <?php echo JText::_('CATEGORY'); ?>
                        </label>
                    </td>
                    <td>
                        <select size="1"
                                class="inputbox"
                                name="category"
                                id="category">
                            <option selected="selected"
                                    value="">
                                <?php echo JText::_('- SELECT CATEGORY -'); ?>
                            </option>
                            <?php foreach ($this->getModel('categories') AS $category) : ?>
                            <option value="<?php echo $category->id; ?>"
                                    <?php echo ($category->id == $faq->category) ? 'selected="selected"' : ''; ?>>
                                <?php echo $category->name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php endif; ?>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <?php echo $this->getModel('editor')->display('answer',  $faq->answer, '100%', '200', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($faq->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('FAQ ID'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo $faq->id; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('STATE'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo JText::_(($faq->published) ? 'PUBLISHED' : 'UNPUBLISHED'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('REVISION'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo ($faq->version) ? $faq->version : JText::_('NOT REVISED'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $faq->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($faq->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $faq->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php endif; ?>
        <?php echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
