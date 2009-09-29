<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

$filteringByTable = false;
$filteringByGroup = false;

$doc =& JFactory::getDocument();
$doc->addStyleDeclaration('.custom-element {width: 100px; float: left;}');
$doc->addStyleDeclaration('.custom-element-separator {width: 30px; float: left; text-align: center;}');

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <?php $filters = $this->getModel('filters'); ?>
    <table>
        <tr>
            <td width="100%">
                &nbsp;
            </td>
            <td nowrap="nowrap">
                <select onchange="submitform();" size="1" class="inputbox" name="filterGroup" id="filterGroup">
                    <option selected="selected" value=""><?php echo '- ' . JText::_('WHD_CD:SELECT GROUP') . ' -'; ?></option>
                    <?php foreach ($filters['groups'] AS $group) : ?>
                    <option value="<?php echo $group->id; ?>"
                            <?php
                            if (@$group->filtering) :
                            echo 'selected="selected"';
                            $filteringByGroup = true;
                            endif;
                            ?>
                            >
                        <?php echo $group->label; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select onchange="submitform();" size="1" class="inputbox" name="filterTable" id="filterTable">
                    <option selected="selected" value=""><?php echo '- ' . JText::_('WHD_CD:SELECT TABLE') . ' -'; ?></option>
                    <?php foreach ($filters['tables'] AS $table) : ?>
                    <option value="<?php echo $table->id; ?>"
                            <?php
                            if (@$table->filtering) :
                            echo 'selected="selected"';
                            $filteringByTable = true;
                            endif;
                            ?>
                            >
                        <?php echo JText::_($table->name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="fields.list" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />
    <input type="hidden" name="targetType"   value="faqcategories" />
    <input type="hidden" name="targetIdentifier" value="faqcategories" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode(JText::_('All Field Permissions')); ?>" />
    <input type="hidden" name="returnURI" value="<?php echo base64_encode(JRoute::_('index.php?option=com_whelpdesk&task=fields.list.start')); ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <?php $fields = $this->getModel(); ?>
    <?php $pagination = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($fields); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'WHD_CD:TABLE',  't.id',     $filters['orderDirection'], $filters['order']); ?>
                    &#9658;
                    <?php echo JHTML::_('grid.sort', 'WHD_CD:GROUP',  'g.id',   $filters['orderDirection'], $filters['order']); ?>
                    &#9658;
                    <?php echo JHTML::_('grid.sort', 'WHD_CD:FIELD',  'f.label',  $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="50px" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'WHD_CD:FIELD LIST',  'f.list',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th class="title" width="10%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'JGrid_Heading_Ordering',  'f.ordering',   $filters['orderDirection'], $filters['order']); ?>
					<?php echo JHtml::_('grid.order',  $fields); ?>
                </th>
                <th  class="title" width="10%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'WHD_CD:FIELD TYPE',  'f.type',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="50" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'WHD_DATA:REVISION',  'f.revision',   $filters['orderDirection'], $filters['order']); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo $pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            for ($i = 0, $n = count($fields); $i < $n; $i++) :
            $field = $fields[$i];
            $field->id = $field->group . '.' . $field->name;
            $ordering = ($filters['order'] == 'f.ordering');
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.checkedout', $field, $i); ?>
                </td>
                <td>
                    <span class="custom-element">
                        <?php echo JText::_($field->tableName); ?>
                    </span>
                    <span class="custom-element-separator">
                        &#9658;
                    </span>
                    <span class="custom-element">
                        <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=datagroup.edit&id=' . $field->group); ?>">
                            <?php echo $field->groupName; ?> 
                        </a>
                    </span>
                    <span class="custom-element-separator">
                        &#9658;
                    </span>
                    <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $field->checked_out)) : ?>
                    <?php echo $field->label; ?>
                    <?php else : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=field.edit&cid[]='.$field->id); ?>">
                        <?php echo htmlspecialchars($field->label, ENT_QUOTES); ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($field->system) : ?>
                    <img border="0" alt="<?php echo JText::_('WHD_CD:SYSTEM FIELD'); ?>" src="components/com_whelpdesk/assets/icons/gear.png"/>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <?php if ($field->list) : ?>
                    <img border="0" 
                         alt="<?php echo JText::_('YES'); ?>"
                         src="images/tick.png"/>
                    <?php else : ?>
                    <img border="0" 
                         alt="<?php echo JText::_('NO'); ?>" 
                         src="images/publish_x.png"/>
                    <?php endif; ?>
                </td>
                <td class="order">
                    <span><?php echo $pagination->orderUpIcon(
                                         $i,
                                         ($filteringByGroup && $filters['order'] == 'f.ordering'),
                                         'items.orderup',
                                         'JGrid_Move_Up',
                                         $ordering
                                     ); ?></span>
					<span><?php echo $pagination->orderDownIcon(
                                         $i,
                                         $pagination->total,
                                         ($filteringByGroup && $filters['order'] == 'f.ordering'),
                                         'items.orderdown',
                                         'JGrid_Move_Down',
                                         $ordering
                                     ); ?></span>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $field->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
                </td>
                <td align="center">
                    <?php echo ($field->type); ?>
                </td>
                <td align="center">
                    <?php echo $field->version ? $field->version : ''; ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="4" border="0" align="center" style="margin-top: 1em;">
		<tbody>
            <tr align="center">
                <td>
                    <img height="16"
                         border="0"
                         width="16"
                         alt="Pending"
                         src="components/com_whelpdesk/assets/icons/gear.png" />
                </td>
                <td style="border-right: 1px solid rgb(170, 170, 170);">
                    <?php echo JText::_('WHD_CD:SYSTEM NOTICE'); ?> 
                </td>
                <td>
                    <img height="16" 
                         border="0"
                         width="16"
                         alt="Pending"
                         src="images/tick.png"/>
                </td>
                <td style="border-right: 1px solid rgb(170, 170, 170);">
                    <?php echo JText::_('WHD_CD:FIELD LISTED NOTICE'); ?>
               </td>
                <td>
                    <img height="16"
                         border="0"
                         width="16"
                         alt="Pending"
                         src="images/publish_x.png"/>
                </td>
                <td>
                    <?php echo JText::_('WHD_CD:FIELD NOT LISTED NOTICE'); ?>
                </td>
            </tr>
		</tbody>
    </table>

</form>
