<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.tooltip');

$document = JFactory::getDocument();

?>

<?php WDocumentHelper::render(); ?>

<?php $field = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm" >

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $field->group; ?>.<?php echo $field->name; ?>" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-50">
        <h2>SELECT GROUP</h2>
        <?php $table = null; ?>
        <?php $groups = $this->getModel('groups'); ?>
        <?php for($i = 0, $c = count($groups); $i < $c; $i++) : ?>
        <?php $group = $groups[$i]; ?>
        <?php if ($table != $group->tableName) : ?>
        <?php $table = $group->tableName; ?>
        <p><?php echo JText::_($group->tableName); ?></p>
        <blockquote>
        <?php endif; ?>
        <input
            type="radio"
            value="<?php echo $group->id; ?>"
            name="group"
            <?php echo $group->id == $this->getModel('selectedGroup') ? 'checked' : ''; ?> />
        <?php echo $group->label; ?><br/>
        <?php if ($table != @$groups[$i+1]->tableName) : ?>
        </blockquote>
        <?php endif; ?>
        <?php endfor; ?>
    </div>
    <div class="col width-50">
        <h2>SELECT TYPE</h2>
        <?php foreach($this->getModel('fieldTypes') AS $type) : ?>
        <input
            type="radio"
            value="<?php echo $type; ?>"
            name="type"
            <?php echo $type == $this->getModel('selectedType') ? 'checked' : ''; ?> />
        <?php echo $type; ?><br/>
        <?php endforeach; ?>
    </div>

</form>
