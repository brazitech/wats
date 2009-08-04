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
                       size="60"
                       id="search"
                       value="<?php echo $filters['search'];?>"
                       class="text_area"
                       onchange="this.adminForm.submit();" />
                <button onclick="this.form.submit();">
                    <?php echo JText::_("GO"); ?>
                </button>
                <button onclick="document.getElementById('search').value=''; this.form.submit();">
                    <?php echo JText::_("RESET"); ?>
                </button>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="faqcategories.list" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />
    <input type="hidden" name="targetType"   value="faqcategories" />
    <input type="hidden" name="targetIdentifier" value="faqcategories" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode(JText::_('All FAQ Permissions')); ?>" />
    <input type="hidden" name="returnURI" value="<?php echo base64_encode(JRoute::_('index.php?option=com_whelpdesk&task=faqcategories.list.start')); ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <?php $categories = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($categories); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'CATEGORY', 'f.name',       $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JText::_('WHD:LIST'); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JText::_('WHD:DISPLAY'); ?>
                </th>
                <th  class="title" width="15%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Creator',    'u.name',    $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Date',      'f.created',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_DATA:REVISION', 'f.revised',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_FAQ:FAQS',     'pages',     $filters['orderDirection'], $filters['order']); ?>
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
            for ($i = 0, $n = count($categories); $i < $n; $i++) :
            $category = $categories[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.checkedout', $category, $i); ?>
                </td>
                <td>
                    <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $category->checked_out)) : ?>
                    <?php echo $category->term; ?>
                    <?php else : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=faqcategory.edit&cid[]='. $category->id); ?>">
                        <?php echo htmlspecialchars($category->name, ENT_QUOTES); ?>
                    </a><br/>
                    <small>(<?php echo htmlspecialchars($category->alias, ENT_QUOTES); ?>)</small>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <a href="<?php echo 'index.php?option=com_whelpdesk&task=faq.list&filterCategory='.$category->id; ?>">
                        <img src="components/com_whelpdesk/assets/icons/view_text-16.png"
                             alt="<?php echo JText::_('LIST'); ?>">
                    </a>
                </td>
                <td align="center">
                    <a href="<?php echo 'index.php?option=com_whelpdesk&task=faqcategory.display&id='.$category->id; ?>">
                        <img src="components/com_whelpdesk/assets/icons/imagegallery-16.png"
                             alt="<?php echo JText::_('BROWSE'); ?>">
                    </a>
                </td>
                <td align="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $category->author); ?>">
                        <?php echo $category->authorName; ?>
                    </a><br/>
                    <small>(<?php echo $category->authorUsername; ?>)</small>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo JHTML::_('date',  $category->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo $category->revised ? $category->revised : ''; ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo $category->pages; ?>
                </td>
                <?php if(count($this->getModel('customFields'))) : ?>
                <?php foreach($this->getModel('customFields') as $field) : ?>
                <td align="center">
                    <?php echo $field->getFormattedValue($category->{$field->getName()}); ?>
                </td>
                <?php endforeach; ?>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</form>
