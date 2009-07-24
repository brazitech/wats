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
                       onchange="this.adminForm.submit();"
                       size="60" />
                <button onclick="this.form.submit();">
                    <?php echo JText::_('GO'); ?>
                </button>
                <button onclick="document.getElementById('search').value='';
                                 document.getElementById('filterCategory').value='';
                                 this.form.submit();">
                    <?php echo JText::_('RESET'); ?>
                </button>
            </td>
            <td nowrap="nowrap">
                <select onchange="submitform();" size="1" class="inputbox" name="filterCategory" id="filterCategory">
                    <option selected="selected" value=""><?php echo JText::_('- SELECT CATEGORY -'); ?></option>
                    <?php foreach ($filters['categories'] AS $category) : ?>
                    <option value="<?php echo $category->id; ?>"
                            <?php echo (@$category->filtering) ? 'selected="selected"' : ''; ?>>
                        <?php echo $category->name; ?> 
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php echo JHTML::_('grid.state', $filters['state']); ?>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="faq.list" />
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

    <?php $faqs = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($faqs); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'WHD_FAQ:QUESTION', 'f.question',  $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Published', 'f.published', $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="15%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Category',  'f.category',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="15%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Author',    'u.name',     $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Date',      'f.created',    $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_DATA:Revision',  'f.version',     $filters['orderDirection'], $filters['order']); ?>
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
            for ($i = 0, $n = count($faqs); $i < $n; $i++) :
            $faq = $faqs[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.checkedout', $faq, $i); ?>
                </td>
                <td>
                    <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $faq->checked_out)) : ?>
                    <?php echo $faq->question; ?>
                    <?php else : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=faq.edit&cid[]='. $faq->id); ?>">
                        <?php echo htmlspecialchars($faq->question, ENT_QUOTES); ?>
                    </a>
                    <br/>
                    <small>(<?php echo htmlspecialchars($faq->alias, ENT_QUOTES); ?>)</small>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.published', $faq->published, $i, 'tick.png', 'publish_x.png', 'faq.state.'); ?>
                </td>
                <td align="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=faqcategory.edit&id=' . $faq->category); ?>">
                        <?php echo $faq->categoryName; ?>
                    </a><br/>
                    <small>(<?php echo $faq->categoryAlias; ?>)</small>
                </td>
                <td align="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $faq->author); ?>">
                        <?php echo $faq->authorName; ?>
                    </a><br/>
                    <small>(<?php echo $faq->authorUsername; ?>)</small>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo JHTML::_('date',  $faq->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo ($faq->version) ? $faq->version : ''; ?>
                </td>
                <?php if(count($this->getModel('customFields'))) : ?>
                <?php foreach($this->getModel('customFields') as $field) : ?>
                <td align="center">
                    <?php echo $field->getFormattedValue($faq->{$field->getName()}); ?>
                </td>
                <?php endforeach; ?>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</form>
