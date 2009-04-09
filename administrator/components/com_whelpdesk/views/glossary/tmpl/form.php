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
$document->addScriptDeclaration("function populateAlias() {if (document.getElementById('alias').value == '') {document.getElementById('alias').value = document.getElementById('term').value.toLowerCase().replace(/(\s+)/g, '-').replace(/([^a-z0-9\-\_\.\(\)])/g, '')}}");

?>


<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="glossary.create" />
    <input type="hidden" name="stage"  value="commit" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <?php $term = $this->getModel(); ?>
    <div class="col width-70">
        <fieldset class="adminform">
            <table  class="admintable">
                <tr>
                    <td class="key">
                        <label for="term">
                            <?php echo JText::_('Term'); ?>
                        </label>
                    </td>
                    <td>
                        <input class="inputbox"
                               type="text"
                               name="term"
                               id="term"
                               size="40"
                               maxlength="500"
                               value="<?php echo $term->term; ?>"
                               onchange="populateAlias();" />
                    </td>
                    <td class="key">
                        <label>
                            <?php echo JText::_('Published'); ?>
                        </label>
                    </td>
                    <td>
                        <select class="inputbox" name="published" id="published">
                            <option selected="selected" value="1">Published</option>
                            <option value="0">Unpublished</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <label for="alias">
                            <?php echo JText::_('Alias'); ?>
                        </label>
                    </td>
                    <td colspan="3">
                        <input class="inputbox" type="text" name="alias" id="alias" size="40" maxlength="255" value="<?php echo $term->alias; ?>" />
                    </td>
                </tr>
            </table>
            <table class="admintable" width="100%">
                <tr>
                    <td>
                        <?php echo $this->getModel('editor')->display('description',  $term->description, '100%', '200', '75', '20', false) ; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>

    <div class="col width-30">
        <?php if ($term->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver;">
            <table class="admintable" style="padding: 5px; margin-bottom: 10px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('Term ID'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo $term->id; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('State'); ?></strong>
                    </td>
                    <td>
                        <?php echo $term->published ? JText::_('Published') : JText::_('UnPublished');?>
                    </td>
                </tr>
                <tr>
                <td>
                <strong><?php echo JText::_('Hits'); ?></strong>
                </td>
                <td>
                <?php echo $term->hits;?>
                <span <?php echo $visibility; ?>>
                <input name="reset_hits" type="button" class="button" value="<?php echo JText::_('Reset'); ?>" onclick="javascript: submitbutton('resethits');" />
                </span>
                </td>
                </tr>
                <tr>
                <td>
                <strong><?php echo JText::_('Revised'); ?></strong>
                </td>
                <td>
                <?php echo $term->version;?> <?php echo JText::_('times'); ?>
                </td>
                </tr>
                <tr>
                <td>
                <strong><?php echo JText::_('Created'); ?></strong>
                </td>
                <td>
                <?php
                if ($term->created == $nullDate) {
                echo JText::_('New document');
                } else {
                echo JHtml::_('date',  $term->created,  JText::_('DATE_FORMAT_LC2'));
                }
                ?>
                </td>
                </tr>
                <tr>
                <td>
                <strong><?php echo JText::_('Modified'); ?></strong>
                </td>
                <td>
                <?php
                if ($term->modified == $nullDate) {
                echo JText::_('Not modified');
                } else {
                echo JHtml::_('date',  $term->modified, JText::_('DATE_FORMAT_LC2'));
                }
                ?>
                </td>
                </tr>
            </table>
        </fieldset>
        <?php endif; ?>
        <!--<fieldset class="adminform">
            <legend><?php echo JText::_('Parameters'); ?></legend>-->
            <?php
            jimport('joomla.html.pane');
            $pane =& JPane::getInstance('sliders');
            echo $pane->startPane("menu-pane");

            $groups = $term->params->getGroups();
            if (count($groups)) {
                foreach($groups AS $groupname => $group) {
                    if ($groupname == '_default') {
                        $title = 'Term';
                    } else {
                        $title = ucfirst($groupname);
                    }
                    if ($term->params->getNumParams($groupname)) {
                        echo $pane->startPanel(JText :: _($title), $groupname.'-page');
                        echo $term->params->render('params', $groupname);
                        echo $pane->endPanel();
                    }
                }
            }
            echo $pane->endPane();
            ?>
        <!--</fieldset>-->
</div>

</form>
