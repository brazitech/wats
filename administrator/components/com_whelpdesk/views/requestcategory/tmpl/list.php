<?php
/**
 * @version $Id: list.php 167 2009-08-21 15:17:14Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

$list = $this->getModel();
$rows = $list->getRows();
$filters = $list->getFilters();
$pagination = $list->getPagination();
//var_dump($list);

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <?php $filters1 = $this->getModel('filters'); ?>
    <!--<table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text"
                       name="search"
                       id="search"
                       value="<?php echo $filters1['search'];?>"
                       class="text_area"
                       size="60"
                       onchange="this.adminForm.submit();" />
                <button onclick="this.form.submit();">
                    <?php echo JText::_("GO"); ?>
                </button>
                <button onclick="document.getElementById('search').value=''; this.form.getElementById('filter_state').value=''; this.form.submit();">
                    <?php echo JText::_("RESET"); ?>
                </button>
            </td>
            <td nowrap="nowrap">
                <?php echo JHTML::_('grid.state', $filters1['state']); ?>
            </td>
        </tr>
    </table>-->

    <fieldset id="filter-bar">
        <div class="fltlft">
        <?php
        foreach ($list->getFilters() as $filter) :
            echo ($filter->getPosition() == 'left') ? $filter->render() : '';
        endforeach;
        ?>
        </div>

        <div class="fltrt">
        <?php
        foreach ($list->getFilters() as $filter) :
            echo ($filter->getPosition() == 'right') ? $filter->render() : '';
        endforeach;
        ?>
        </div>
    </fieldset>

    <div class="clr"></div>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="<?php echo WFactory::getCommand(); ?>" />
    <!--<input type="hidden" name="targetType"   value="glossary" />-->
    <!--<input type="hidden" name="targetIdentifier" value="glossary" />-->
    <!--<input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode('glossary'); ?>" />-->
    <!--<input type="hidden" name="returnURI" value="<?php echo base64_encode(JRoute::_('index.php?option=com_whelpdesk&task=glossary.list.start')); ?>" />-->
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters1['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters1['orderDirection']; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
            <?php
            foreach ($list->getColumns() as $filter) :
                echo $filter->renderHeader('ASC', 'name');
            endforeach;
            ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="<?php echo count($list->getColumns()); ?>">
                    <?php echo $pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        for ($i = 0, $n = count($rows); $i < $n; $i++) :
        $row = $rows[$i];
        ?>
            <tr class="row<?php echo $pagination->getRowOffset($i-1); ?>">
                <!--<td>
                    ?php echo $pagination->getRowOffset($i); ?>
                </td>-->
            <?php
            foreach ($list->getColumns() as $filter) :
                echo $filter->render($row);
            endforeach;
            ?>
            </tr>
        <?php endfor; ?>
        </tbody>
    </table>

</form>
