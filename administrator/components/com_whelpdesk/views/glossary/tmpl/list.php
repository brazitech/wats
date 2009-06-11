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
                <button onclick="document.getElementById('search').value=''; this.form.getElementById('filter_state').value=''; this.form.submit();">
                    <?php echo JText::_("RESET"); ?>
                </button>
            </td>
            <td nowrap="nowrap">
                <?php echo JHTML::_("grid.state", $filters["state"]); ?>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="glossary.list" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="limit"        value="0" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <input type="hidden" name="filter_order" value="<?php echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <?php $glossaryItems = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($glossaryItems); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'Term',     'term',       $filters['orderDirection'], $filters['order']); ?>
                    (<?php echo JHTML::_('grid.sort', 'Alias',   'alias',      $filters['orderDirection'], $filters['order']); ?>)
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Published', 'published', $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="15%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Author',    'author',    $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Date',      'created',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Revision',  'version',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Hits',      'hits',      $filters['orderDirection'], $filters['order']); ?>
                </th>
                <?php if(count($this->getModel('customFields'))) : ?>
                <?php foreach($this->getModel('customFields') as $field) : ?>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', $field->getLabel(), $field->getName(), $filters['orderDirection'], $filters['order']); ?>
                </th>
                <?php endforeach; ?>
                <?php endif; ?>
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
            for ($i = 0, $n = count($glossaryItems); $i < $n; $i++) :
            $glossaryItem = $glossaryItems[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
            <td>
            <?php echo $pagination->getRowOffset($i); ?>
            </td>
            <td align="center">
            <?php echo JHTML::_('grid.checkedout', $glossaryItem, $i); ?>
            </td>
            <td>
                <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $glossaryItem->checked_out)) : ?>
                <?php echo $glossaryItem->term; ?>
                <?php else : ?>
                <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=glossary.edit&cid[]='. $glossaryItem->id); ?>">
                    <?php echo htmlspecialchars($glossaryItem->term, ENT_QUOTES); ?>
                </a>
                (<?php echo htmlspecialchars($glossaryItem->alias, ENT_QUOTES); ?>)
                <?php endif; ?>
            </td>
            <td align="center">
            <?php echo JHTML::_('grid.published', $glossaryItem->published, $i, 'tick.png', 'publish_x.png', 'glossary.state.'); ?>
            </td>
            <td align="center">
                <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $glossaryItem->author); ?>">
                    <?php echo $glossaryItem->authorName; ?>
                </a>
                (<?php echo $glossaryItem->authorUsername; ?>)
            </td>
            <td nowrap="nowrap" align="center">
                <?php echo JHTML::_('date',  $glossaryItem->created, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
            <td nowrap="nowrap" align="center">
                <?php echo $glossaryItem->version ?>
            </td>
            <td nowrap="nowrap" align="center">
                <?php echo $glossaryItem->hits ?>
            </td>
            <?php if(count($this->getModel('customFields'))) : ?>
            <?php foreach($this->getModel('customFields') as $field) : ?>
            <td align="center">
                <?php echo $field->getFormattedValue($glossaryItem->{$field->getName()}); ?>
            </td>
            <?php endforeach; ?>
            <?php endif; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</form>
