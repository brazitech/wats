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
$document->addScriptDeclaration("function populateAlias() {if (document.getElementById('alias').value == '') {document.getElementById('alias').value = document.getElementById('name').value.toLowerCase().replace(/(\s+)/g, '-').replace(/([^a-z0-9\-\_\.\(\)])/g, '')}}");

$knowledgeDomain = $this->getModel('knowledgeDomain');
$knowledge = $this->getModel();
$knowledgeRevision = $this->getModel('knowledgeRevision');

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="alias"  value="<?php echo $knowledge->alias; ?>" />
    <input type="hidden" name="domain" value="<?php echo $knowledgeDomain->alias; ?>" />
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
                               id="name"
                               name="name"
                               size="40"
                               maxlength="500"
                               value="<?php echo $knowledge->name; ?>" />
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
                               id="alias"
                               name="newAlias"
                               size="40"
                               maxlength="255"
                               value="<?php echo $knowledge->alias; ?>" />
                    </td>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <h2 style="margin: 0;"><?php echo JText::_('WHD_KD:KNOWLEDGE PAGE'); ?></h2>
                        <?php echo $this->getModel('editor')->display('content',  ($knowledgeRevision ? $knowledgeRevision->content : ''), '100%', '500', '75', '20', false) ; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h2 style="margin: 0;"><?php echo JText::_('WHD_KD:KNOWLEDGE COMMENT'); ?></h2>
                        <?php echo $this->getModel('editor')->display('comment',  '', '100%', '100', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($knowledgeDomain->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <?php if ($knowledgeRevision) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:REVISION'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledgeRevision->revision; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledgeRevision->created; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:MODIFIED BY'); ?></strong>
                    </td>
                    <td>
                        <?php echo JFactory::getUser($knowledgeRevision->created_by)->name; ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledge->created; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED BY'); ?></strong>
                    </td>
                    <td>
                        <?php echo JFactory::getUser($knowledge->created_by)->name; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php endif; ?>

        <?php if ($knowledgeDomain->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_KD:DOMAIN'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledgeDomain->name; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('State'); ?></strong>
                    </td>
                    <td>
                        <?php echo $knowledgeDomain->published ? JText::_('Published') : JText::_('UnPublished');?>
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
                        <strong><?php echo JText::_('WHD_DATA:MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHtml::_('date',  $knowledgeDomain->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php endif; ?>

<?php if ($knowledgeRevision) : ?>
<?php wimport('helper.knowledge'); ?>
<?php $sections = WKnowledgeHelper::getSections($knowledgeRevision->content, null); ?>
<?php if ($c1 = count($sections)) : ?>
<fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
    <h2 style="padding: 10px 10px 0px 10px; margin: 0px;">
        <?php echo JText::_('WHD_KD:CONTENTS'); ?>
    </h2>
    <ul style="margin: 0px; padding: 5px;">
        <?php for ($i1 = 0; $i1 < $c1; $i1++) : ?>
        <li>
            <?php echo $sections[$i1]['section']; ?>
            <?php if ($c2 = count($sections[$i1]['children'])) : ?>
            <ul>
                <?php for ($i2 = 0; $i2 < $c2; $i2++) : ?>
                <li>
                    <?php echo $sections[$i1]['children'][$i2]['section']; ?>
                    <?php if ($c3 = count($sections[$i1]['children'][$i2]['children'])) : ?>
                    <ul>
                        <?php for ($i3 = 0; $i3 < $c3; $i3++) : ?>
                        <li>
                            <?php echo $sections[$i1]['children'][$i2]['children'][$i3]['section']; ?>
                        </li>
                        <?php endfor; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endfor; ?>
            </ul>
            <?php endif; ?>
        </li>
        <?php endfor; ?>
    </ul>
</fieldset>
<?php endif; ?>
</div>
<?php endif; ?>


</form>
