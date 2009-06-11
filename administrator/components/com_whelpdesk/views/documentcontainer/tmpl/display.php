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

<?php $container = $this->getModel(); ?>
<?php $documentcontainers = $this->getModel('documentcontainers'); ?>
<?php $documents = $this->getModel('documents'); ?>
<?php $parents = $this->getModel('parents'); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="id"     value="<?php echo $container->id; ?>" />
    <input type="hidden" name="parent" value="<?php echo $container->id; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <?php if (count($documentcontainers) == 0 && count($documents) == 0) : ?>
        <p>
            <?php echo JText::sprintf('DOCUMENT CONTAINER %s IS EMPTY', $container->name); ?>
        </p>
        <?php else : ?>
        <?php if (count($documentcontainers) > 0) : ?>
        <h2 style="margin-top: 0;">Folders</h2>
        <?php for ($i = 0, $c = count($documentcontainers) ; $i < $c ; $i++) : ?>
        <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$documentcontainers[$i]->id); ?>">
            <div style="float: left;
                        height: 7.8em;
                        width: 7.3em;
                        border: 1px solid #FFFFFF;
                        margin: 1em;
                        text-align: center;
                        padding: 0.5em;
                        overflow: hidden;"
                 onmouseover="javascript: this.style.border = '1px solid #CCCCCC'; this.style.background = '#F3F7FD';"
                 onmouseout="javascript: this.style.border = '1px solid #FFFFFF'; this.style.background = 'none';"
                 class="hasTip"
                 title="<?php echo $documentcontainers[$i]->name . '::' . $documentcontainers[$i]->description; ?>">
                <img src="components/com_whelpdesk/assets/icons/folder_gray.png"
                     border="0"><br/>
                <?php echo $documentcontainers[$i]->name; ?>
            </div>
        </a>
        <?php endfor; ?>
        <?php endif; ?>
        <?php if (count($documents) > 0) : ?>
        <div style="clear: both;"></div>
        <h2 style="<?php echo (count($documentcontainers) == 0) ? ' margin-top: 0;' : ''; ?>">Files</h2>
        <?php for ($i = 0, $c = count($documents) ; $i < $c ; $i++) : ?>
        <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=document.download&id='.$documents[$i]->id); ?>">
            <div style="float: left;
                        height: 7.8em;
                        width: 7.3em;
                        margin: 1em;
                        text-align: center;
                        border: 1px solid #FFFFFF;
                        padding: 0.5em;"
                 onmouseover="javascript: this.style.border = '1px solid #CCCCCC'; this.style.background = '#F3F7FD';"
                 onmouseout="javascript: this.style.border = '1px solid #FFFFFF'; this.style.background = 'none';"
                 class="hasTip"
                 title="<?php echo $documents[$i]->name . '::' . $documents[$i]->description; ?>">
                <img src="components/com_whelpdesk/assets/icons/unknown.png"
                     border="0"><br/>
                <?php echo $documents[$i]->name; ?>
            </div>
        </a>
        <?php endfor; ?>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col width-30">
        <?php if ($container->id) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('DOCUMENT CONTAINER'); ?></strong>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomain.display&domain='.$container->alias); ?>">
                            <?php echo $container->name; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $container->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($container->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $container->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </fieldset>
        <?php if (strlen($container->description)) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px; padding: 10px">
            <?php echo $container->description; ?>
        </fieldset>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</form>
