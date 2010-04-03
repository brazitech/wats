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

?>

<?php WDocumentHelper::render(); ?>

<?php if (!$this->getModel('canDownload') && JRequest::getBool('modal')) : ?>
    <div style="background-color: #E6C0C0;
                color: #CC0000;
                border-top: 3px solid #DE7A7B;
                border-bottom: 3px solid #DE7A7B;
                margin: 8px 0;
                padding: 5px;
                font-weight: bold;">
        <?php echo JText::_('YOU DO NOT HAVE THE NECESARY PERMISSIONS TO DOWNLOAD THIS FILE'); ?>
    </div>
<?php elseif (!$this->getModel('canDownload')) : ?>
<?php JError::raiseWarning('403', JText::_('YOU DO NOT HAVE THE NECESARY PERMISSIONS TO DOWNLOAD THIS FILE')); ?>
<?php endif; ?>


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

    <div class="col width-60">
        <div style="text-align: center;">
            <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=document.download&id='.$document->id); ?>">
                <img src="components/com_whelpdesk/assets/mimetypes/large/<?php
                $icon = strtolower(substr(strrchr($document->filename, '.'), 1));
                if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'mimetypes' . DS . 'large' . DS . $icon . '.png')) {
                    $icon = 'unknown';
                }
                echo $icon;
                ?>.png"
                     border="0"
                     title="<?php echo JText::_('DOWNLOAD'); ?>"
                     style="margin: 1.5em;"><br />
                <?php echo JText::_('WHD_DOC:DOCUMENT DOWNLOAD'); ?>
            </a>
        </div>
        <div>
            <?php echo $document->description; ?>
        </div>
    </div>

    <div class="col width-40">
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px; width: 100%;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:CREATED BY'); ?></strong>
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
                        <strong><?php echo JText::_('WHD_DATA:CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $document->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($document->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DATA:MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $document->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD:WWWADDRESS'); ?></strong>
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
                        <strong><?php echo JText::_('WHD_DOC:DOCUMENT DOWNLOADS'); ?></strong>
                    </td>
                    <td>
                        <?php echo $document->hits;?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WHD_DOC:DOCUMENT SIZE'); ?></strong>
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
        <?php //echo $this->loadLayout('fieldset_simpleform'); ?>
</div>

</form>
