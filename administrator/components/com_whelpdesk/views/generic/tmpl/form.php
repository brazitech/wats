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
jimport('joomla.html.pane');

$pane =& JPane::getInstance('sliders');
$form = $this->getModel();
?>

<!-- Helpdesk Document Header -->
<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      oonsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <?php echo JHTML::_('form.token'); ?>

    <!-- normal fieldsets -->
    <div class="width-60 fltlft">
        <?php
        // Iterate through the normal form fieldsets and display each one.
        foreach ($form->getFieldsets('normal') as $group => $fieldset):
        ?>
        <fieldset class="adminform">
            <legend>
                <?php if (isset($fieldset['label'])): ?>
                <?php echo JText::_($fieldset['label']); ?>
                <?php elseif (isset($fieldset['labelfield'])): ?>
                <?php echo $form->getValue($fieldset['labelfield'], WDocumentHelper::subtitle(), $group); ?>
                <?php else: ?>
                <?php echo WDocumentHelper::subtitle(); ?>
                <?php endif; ?>
            </legend>
            <dl>
            <?php
            // Iterate through the fields in the set and display them.
            foreach($form->getFields($group) as $field):
                // If the field is hidden, just display the input.
                if ($field->hidden):
                    echo $field->input;
                else:
                ?>
                    <dt>
                        <?php echo $field->label; ?>
                    </dt>
                    <dd<?php echo ($field->getType() == 'Editor' || $field->getType() == 'Textarea' || $field->getType() == 'TextOnly' || $field->getType() == 'RequestHistory') ? ' style="clear: both; margin: 0;"' : ''?>>
                        <?php echo $field->input ?>
                    </dd>
                <?php
                endif;
            endforeach;
            ?>
            </dl>
        </fieldset>
        <?php
        endforeach;
        ?>

        <?php if (isset($onAfterNormalFieldSets)) : ?>
        <?php echo $onAfterNormalFieldSets; ?>
        <?php endif; ?>
    </div>

    <!-- detail fieldsets -->
    <div class="width-40 fltrt" style="margin-top: 17px;">
        <?php echo $pane->startPane("detail"); ?>

            <?php
            // Iterate through the detail form fieldsets and display each one.
            foreach ($form->getFieldsets("detail") as $group => $fieldset):
            ?>
            <?php echo $pane->startPanel(JText::_($fieldset['label']), $group); ?>
            <fieldset class="panelform">
                <dl>
                <?php

                // Iterate through the fields in the set and display them.
                foreach($form->getFields($group) as $field):
                    // If the field is hidden, just display the input.
                    if ($field->hidden):
                        echo $field->input;
                    else:
                    ?>
                        <dt>
                            <?php echo $field->label; ?>
                        </dt>
                        <dd<?php echo ($field->getType() == 'Editor' || $field->getType() == 'Textarea' || $field->getType() == 'TextOnly' || $field->getType() == 'RequestHistory') ? ' style="clear: both; margin: 0;"' : ''?>>
                            <?php echo $field->input; ?>
                        </dd>
                    <?php
                    endif;
                endforeach;
                ?>
                </dl>
            </fieldset>
            <?php echo $pane->endPanel(); ?>
            <?php
            endforeach;
            ?>
        <?php echo $pane->endPane(); ?>
    </div>

</form>
