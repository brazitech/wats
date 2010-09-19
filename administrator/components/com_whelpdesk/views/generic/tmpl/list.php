<?php
/**
 * @version $Id$
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

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <?php $filters1 = $this->getModel('filters'); ?>

    <?php if (count($list->getFilters())) : ?>
    <fieldset id="filter-bar" style="clear: both; height: auto !important;">
        <div class="fltlft">
        <?php
        foreach ($list->getFilters() as $column) :
            echo ($column->getPosition() == 'left') ? $column->render() : '';
        endforeach;
        ?>
        </div>

        <div class="fltrt">
        <?php
        foreach ($list->getFilters() as $column) :
            echo ($column->getPosition() == 'right') ? $column->render() : '';
        endforeach;
        ?>
        </div>
    </fieldset>
    <?php endif; ?>

    <div class="clr"></div>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="<?php echo WFactory::getCommand(); ?>" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters1['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters1['orderDirection']; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
            <?php
            foreach ($list->getColumns() as $column) :
                echo $column->renderHeader('ASC', 'name');
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
        <?php while (!is_null($list->nextRow())) : ?>
            <tr class="row<?php echo $pagination->getRowOffset($list->getRowPointer() - 1) % 2; ?>">
            <?php
            foreach ($list->getColumns() as $column) :
                echo $column->render($list);
            endforeach;
            ?>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</form>
