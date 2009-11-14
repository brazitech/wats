<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

// get the groups from the datset to process
$groups = $this->getModel('fieldset')->getGroupNames();
$data   = $this->getModel('fieldset-data');

?>

    <?php for($i = 0, $c = count($groups); $i < $c; $i++) : ?>
    <?php $group = $this->getModel('fieldset')->getGroup($groups[$i]); ?>
    <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
        <table class="admintable" style="padding: 0px; margin-bottom: 0px;">
            <tbody>
            <?php for($z = 0, $t = count($group->fields); $z < $t; $z++) : ?>
            <?php $field = $group->fields[$z]; ?>
            <tr>
                <td>
                    <strong>
                        <?php echo $field->getLabel(); ?>
                    </strong>
                </td>
                <td>
                    <?php echo $field->getHTML($data->{$field->getName()}); ?>
                </td>
            </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </fieldset>
    <?php endfor; ?>
