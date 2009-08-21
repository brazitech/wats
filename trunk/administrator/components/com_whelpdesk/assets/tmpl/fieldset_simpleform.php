<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

// get the sliders
jimport('joomla.html.pane');
$pane =& JPane::getInstance('sliders');

// get the groups from the datset to process
$groups = $this->getModel('fieldset')->getGroupNames();
$data   = $this->getModel('fieldset-data');

?>

<?php echo $pane->startPane("fieldset-pane"); ?>

    <?php for($i = 0, $c = count($groups); $i < $c; $i++) : ?>
    <?php $group = $this->getModel('fieldset')->getGroup($groups[$i]); ?>
    <?php echo $pane->startPanel(JText:: _($group->label), $groups[$i].'-panel'); ?>
        <table class="paramlist admintable" width="100%" cellspacing="1">
            <tbody>
            <?php for($z = 0, $t = count($group->fields); $z < $t; $z++) : ?>
            <?php $field = $group->fields[$z]; ?>
            <tr>
                <td class="paramlist_key">
                    <label class="hasTip" 
                           for="<? echo $field->getName(); ?>"
                           title="<?php echo $field->getLabel(); ?>::<?php echo $field->getDescription(); ?>">
                        <?php echo $field->getLabel(); ?>
                    </label>
                </td>
                <td class="paramlist_value">
                    <?php //echo $field->getHTML_FormElement($data->{'field_'.$groups[$i].'_'.$field->getName()}); ?>
                    <?php echo $field->getHTML_FormElement($data->{$field->getFullName()}); ?>
                </td>
            </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    <?php echo $pane->endPanel(); ?>
    <?php endfor; ?>

<?php echo $pane->endPane(); ?>
