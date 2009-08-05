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
                <!--
                <select onchange="submitform();" size="1" class="inputbox" name="filterDomain" id="filterDomain">
                    <option selected="selected" value=""><?php echo JText::_('- SELECT DOMAIN -'); ?></option>
                    <?php foreach ($filters['categories'] AS $category) : ?>
                    <option value="<?php echo $category->id; ?>"
                            <?php echo (@$category->filtering) ? 'selected="selected"' : ''; ?>>
                        <?php echo $category->name; ?> 
                    </option>
                    <?php endforeach; ?>
                </select>
                -->
            </td>
        </tr>
    </table>

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="knowledge.list" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters['orderDirection']; ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <?php $knowledge = $this->getModel(); ?>
    <?php $pagination    = $this->getModel('pagination'); ?>
    <table class="adminlist" cellspacing="1">
        <thead>
            <tr>
                <th width="5">
                    <?php echo JText::_('Num'); ?>
                </th>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($knowledge); ?>);" />
                </th>
                <th class="title">
                    <?php echo JHTML::_('grid.sort', 'WHD_KD:KNOWLEDGE', 'k.name',  $filters['orderDirection'], $filters['order']); ?>
                </th>
                <th width="60" nowrap="nowrap">
                    <?php echo JText::_('WHD:DISPLAY'); ?>
                </th>
                <th  class="title" width="50" nowrap="nowrap">
                    <?php echo JText::_('WHD:STATUS'); ?>
                </th>
                <th  class="title" width="50" nowrap="nowrap">
                    <?php echo JText::_('WHD_LINKER:LINKS TO'); ?>
                </th>
                <th  class="title" width="50" nowrap="nowrap">
                    <?php echo JText::_('WHD_LINKER:LINKS FROM'); ?>
                </th>
                <th align="center" width="15%">
                    <?php echo JHTML::_('grid.sort', 'Updated',      'lastRevised',    $filters['orderDirection'], $filters['order']); ?>
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
            for ($i = 0, $n = count($knowledge); $i < $n; $i++) :
            $page = $knowledge[$i];
            ?>
            <tr class="row<?php echo ($i % 2); ?>">
                <td>
                    <?php echo $pagination->getRowOffset($i); ?>
                </td>
                <td align="center">
                    <?php echo JHTML::_('grid.checkedout', $page, $i); ?>
                </td>
                <td>
                    <?php if (JTable::isCheckedOut(JFactory::getUser()->get('id'), $page->checked_out)) : ?>
                    <?php echo $page->name; ?>
                    <?php else : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=knowledge.edit&cid[]='. $page->id); ?>">
                        <?php echo htmlspecialchars($page->name, ENT_QUOTES); ?>
                    </a>
                    <br/>
                    <small>[knowledge[<?php echo $page->domainAlias; ?>:<?php echo $page->alias; ?>]]</small>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <a href="<?php echo 'index.php?option=com_whelpdesk&task=knowledge.display&id='.$page->id; ?>">
                        <img alt="<?php echo JText::_('WHD:DISPLAY'); ?>"
                             src="components/com_whelpdesk/assets/icons/imagegallery-16.png"/>
                    </a>
                </td>
                <td align="center">
                    <?php if (!$page->latestRevision) : ?>
                    <img alt="<?php echo JText::_('WHD_KD:NO CONTENT'); ?>" 
                         title="<?php echo JText::_('WHD_KD:NO CONTENT'); ?>"
                         src="components/com_whelpdesk/assets/wrench--exclamation.png"/>
                    <?php endif; ?>
                    <?php if ($page->isDefault) : ?>
                    <img title="<?php echo JText::_('Default'); ?>"
                         alt="<?php echo JText::_('Default'); ?>"
                         src="components/com_whelpdesk/assets/home.png"/>
                    <?php elseif (!$page->linksTo) : ?>
                    <img alt="<?php echo JText::_('WHD_KD:NO LINKS'); ?>" 
                         title="<?php echo JText::_('WHD_KD:NO LINKS'); ?>"
                         src="components/com_whelpdesk/assets/chain--exclamation.png"/>
                    <?php endif; ?>
                </td>
                <td align="center">
                    <?php echo $page->linksTo; ?>
                </td>
                <td align="center">
                    <?php echo $page->linksFrom; ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo JHTML::_('date',  $page->lastRevised, JText::_('DATE_FORMAT_LC2')); ?>
                </td>
                <td nowrap="nowrap" align="center">
                    <?php echo ($page->latestRevision) ? $page->latestRevision : ''; ?>
                </td>
                <?php if(count($this->getModel('customFields'))) : ?>
                <?php foreach($this->getModel('customFields') as $field) : ?>
                <td align="center">
                    <?php echo $field->getFormattedValue($page->{$field->getName()}); ?>
                </td>
                <?php endforeach; ?>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="4" border="0" align="center" style="margin-top: 1em;">
		<tbody>
            <tr align="center">
                <td>
                    <img height="16" border="0" width="16" src="components/com_whelpdesk/assets/home.png" alt="Pending"/>
                </td>
                <td style="border-right: 1px solid rgb(170, 170, 170);">
                    <?php echo JText::_('WHD_KD:TIP DEFAULT'); ?>
                </td>
                <td>
                    <img height="16" border="0" width="16" src="components/com_whelpdesk/assets/wrench--exclamation.png" alt="Pending"/>
                </td>
                <td style="border-right: 1px solid rgb(170, 170, 170);">
                    <?php echo JText::_('WHD_KD:TIP EMPTY'); ?>
                </td>
                <td>
                    <img height="16" border="0" width="16" src="components/com_whelpdesk/assets/chain--exclamation.png" alt="Pending"/>
                </td>
                <td>
                    <?php echo JText::_('WHD_KD:TIP NO LINKS'); ?>
                </td>
            </tr>
		</tbody>
    </table>

</form>
