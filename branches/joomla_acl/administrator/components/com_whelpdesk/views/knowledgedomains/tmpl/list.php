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
                <?php echo JHTML::_("grid.state", $filters["state"]); ?>
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="knowledgedomains.list" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />
    <input type="hidden" name="targetType"   value="knowledgedomains" />
    <input type="hidden" name="targetIdentifier" value="knowledgedomains" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode(JText::_('WHD_KD:DOMAINS')); ?>" />
    <input type="hidden" name="returnURI" value="<?php echo base64_encode(JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomains.list.start')); ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <?php $knowledgeDomain = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($knowledgeDomain); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'WHD_KD:DOMAIN', 'k.name',       $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JText::_('WHD:LIST'); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JText::_('WHD:DISPLAY'); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Published', 'k.published', $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th  class="title" width="15%" nowrap="nowrap">
                    <?php echo JHTML::_('grid.sort', 'Creator',    'u.name',    $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'Date',      'k.created',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_DATA:REVISION',  'k.revised',   $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_KD:PAGES',     'pages',     $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th align="center" width="50">
                    <?php echo JHTML::_('grid.sort', 'WHD_KD:PAGES MISSING',     'pagesMissing',     $filters['orderDirection'], $filters['order']); ?>
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
            for ($i = 0, $n = count($knowledgeDomain); $i < $n; $i++) :
            $knowledgeDomainItem = $knowledgeDomain[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.checkedout', $knowledgeDomainItem, $i); ?>
                </td>
                <td>
                    <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $knowledgeDomainItem->checked_out)) : ?>
                    <?php echo $knowledgeDomainItem->term; ?>
                    <?php else : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=knowledgedomain.edit&cid[]='. $knowledgeDomainItem->id); ?>">
                        <?php echo htmlspecialchars($knowledgeDomainItem->name, ENT_QUOTES); ?>
                    </a><br />
                    <small>[knowledgedomain[<?php echo htmlspecialchars($knowledgeDomainItem->alias, ENT_QUOTES); ?>]]</small>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <a href="<?php echo 'index.php?option=com_whelpdesk&task=knowledge.list&filterDomain='.$knowledgeDomainItem->id; ?>">
                        <img alt="<?php echo JText::_('WHD:LIST'); ?>"
                             src="components/com_whelpdesk/assets/icons/view_text-16.png"/>
                    </a>
                </td>
                <td align="center">
                    <a href="<?php echo 'index.php?option=com_whelpdesk&task=knowledgedomain.display&id='.$knowledgeDomainItem->id; ?>">
                        <img alt="<?php echo JText::_('WHD:DISPLAY'); ?>"
                             src="components/com_whelpdesk/assets/icons/imagegallery-16.png"/>
                    </a>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.published', $knowledgeDomainItem->published, $i, 'tick.png', 'publish_x.png', 'knowledgedomain.state.'); ?>
                </td>
                <td align="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $knowledgeDomainItem->author); ?>">
                        <?php echo $knowledgeDomainItem->authorName; ?>
                    </a>
                    (<?php echo $knowledgeDomainItem->authorUsername; ?>)
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo JHTML::_('date',  $knowledgeDomainItem->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo $knowledgeDomainItem->revised ? $knowledgeDomainItem->revised : ''; ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo $knowledgeDomainItem->pages; ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo $knowledgeDomainItem->pagesMissing ? $knowledgeDomainItem->pagesMissing : 0; ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</form>
