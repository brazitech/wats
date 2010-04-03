<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <?php $filters = $this->getModel('filters'); ?>
    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text"
                       name="search"
                       id="search"
                       value="<?php echo $filters['search'];?>"
                       class="text_area"
                       onchange="this.adminForm.submit();" />
                <button onclick="this.form.submit();">
                    <?php echo JText::_("GO"); ?>
                </button>
                <button onclick="document.getElementById('search').value=''; this.form.getElementById('filter_allow').value=''; this.form.submit();">
                    <?php echo JText::_("RESET"); ?>
                </button>
            </td>
            <td nowrap="nowrap">
                <select name="filter_allow"
                        id="filter_allow"
                        onchange="javascript: this.form.submit();">
                    <option value="">- <?php echo JText::_('SELECT STATUS'); ?> -</option>
                    <option value="1"
                            <?php echo ($filters['allow'] == '1') ? 'selected' : '' ; ?>
                            style="margin: 2px;
                                   background-image: url(components/com_whelpdesk/assets/icons/lock-unlock.png);
                                   background-repeat: no-repeat;
                                   padding-left: 24px;">
                        <?php echo JText::_('Allow'); ?>
                    </option>
                    <option value="0"
                            <?php echo ($filters['allow'] == '0') ? 'selected' : '' ; ?>
                            style="margin: 2px;
                                   background-image: url(components/com_whelpdesk/assets/icons/lock.png);
                                   background-repeat: no-repeat;
                                   padding-left: 24px;">
                        <?php echo JText::_('Deny'); ?>
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="permissions.list" />
    <input type="hidden" name="targetIdentifier" value="<?php echo $this->getModel('targetIdentifier'); ?>" />
    <input type="hidden" name="targetType"       value="<?php echo $this->getModel('targetType'); ?>" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode($this->getModel('targetIdentifierAlias')); ?>" />
    <input type="hidden" name="returnURI"        value="<?php echo base64_encode($this->getModel('returnURI')); ?>" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php //echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />

    <?php $rules = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th class="title" width="10">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th  class="title" nowrap="nowrap" width="35%">
                    <?php echo JHTML::_('grid.sort', 'User/User Group', '',                     $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th class="title" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Description',     'controls.description', $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th class="title" align="center" width="80">
                    <?php echo JHTML::_('grid.sort', 'Status',          'allow',                $filters['orderDirection'], $filters['order']); ?>
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
            for ($i = 0, $n = count($rules); $i < $n; $i++) :
            $rule = $rules[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($rule->request_name, ENT_QUOTES); ?>
                    (<?php echo htmlspecialchars($rule->request_alias, ENT_QUOTES); ?>)
                </td>
                <td>
                    <?php echo $rule->control_description; ?>
                </td>
                <td align="center">
                    <?php if ($rule->allow) : ?>
                    <img src="components/com_whelpdesk/assets/icons/lock-unlock.png"
                         alt="" />
                    <?php else : ?>
                    <img src="components/com_whelpdesk/assets/icons/lock.png"
                         alt="" />
                    <?php endif; ?>
                    <?php if ($rule->warning) : ?>
                    <img src="components/com_whelpdesk/assets/icons/exclamation.png"
                         alt="<?php echo JText::_('NOT ALLOWED'); ?>"
                         class="hasTip"
                         title="Cannot allow access"/>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <table cellspacing="0"
           cellpadding="4"
           border="0"
           align="center"
           style="margin-top: 1em;">
		<tbody>
            <tr align="center">
                <td>
                    <img height="16" border="0" width="16" alt="Pending" src="components/com_whelpdesk/assets/icons/lock-unlock.png"/>
                </td>
                <td style="border-right: 1px solid #AAAAAA;">
                    <?php echo JText::_('Allow (a rule exists specifically to allow access)'); ?>
                </td>
                <td>
                    <img height="16" border="0" width="16" alt="Pending" src="components/com_whelpdesk/assets/icons/lock.png"/>
                </td>
                <td style="border-right: 1px solid #AAAAAA;">
                    <?php echo JText::_('Deny (a rule exists specifically to deny access)'); ?>
                </td>
                <td>
                    <img height="16" border="0" width="16" alt="Pending" src="components/com_whelpdesk/assets/icons/exclamation.png"/>
                </td>
                <td>
                    <?php echo JText::_('Denied (although an allow rule exists, there is a deny rule further up the tree)'); ?>
                </td>
            </tr>
		</tbody>
    </table>

</form>
