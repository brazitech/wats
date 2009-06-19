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

<style>
#userlist {
    margin: 0;
    padding: 1em;
    overflow: hidden;
}

#userlist li.selected {
    border: 1px solid #CCCCCC;
    background-color: #F3F7FD;
    font-weight: bold;
}

#userlist li {
    float: left;
    width: 30%;
    padding: 0.8em;
    margin: 0.5em;
    border: 1px solid #FFFFFF;
    background-color: #FBFBFB;
    cursor: pointer;
    text-align: center;
    list-stylee: none;
    list-style-image: url(components/com_whelpdesk/assets/icons/16-user-silhouette.png);
    list-style-position: inside;
}
</style>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"           value="com_whelpdesk" />
    <input type="hidden" name="task"             value=""/>
    <input type="hidden" name="targetIdentifier" value="<?php echo $this->getModel('targetIdentifier'); ?>" />
    <input type="hidden" name="targetType"       value="<?php echo $this->getModel('targetType'); ?>" />
    <input type="hidden" name="requestType"      value="user" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode($this->getModel('targetIdentifierAlias')); ?>" />
    <input type="hidden" name="returnURI"        value="<?php echo base64_encode($this->getModel('returnURI')); ?>" />
    <?php echo JHTML::_('form.token'); ?>

	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text"
                       name="filterSearch"
                       id="filterSearch"
                       value="<?php echo $this->getModel('filterSearch');?>"
                       class="text_area"
                       onchange="javascript: submitform('permissions.edit.findUserRequestNode');" />
				<button onclick="javascript: submitform('permissions.edit.findUserRequestNode');">
                    <?php echo JText::_('Go'); ?>
                </button>
				<button onclick="document.getElementById('filterSearch').value=''; this.form.getElementById('filterGroup').value='0'; submitform('permissions.edit.findUserRequestNode');">
                    <?php echo JText::_('Reset'); ?>
                </button>
			</td>
			<td nowrap="nowrap">
				<select name="filterGroup" 
                        id="filterGroup"
                        class="inputbox"
                        size="1"
                        onchange="javascript: submitform('permissions.edit.findUserRequestNode');">
					<option value="0" <?php echo ($this->getModel('selectedGroup') == 0) ? 'selected' : ''; ?>><?php echo JText::_('ALL GROUPS'); ?></option>
					<?php foreach ($this->getModel('groups') AS $group) : ?>
                    <option value="<?php echo $group->id; ?>" <?php echo ($this->getModel('selectedGroup') == $group->id) ? 'selected' : ''; ?>><?php echo $group->name; ?></option>
                    <?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>

    <?php $users = $this->getModel(); ?>
    <ul id="userlist">
        <?php for ($i=0, $n=count($users); $i < $n; $i++) : ?>
        <li style=""
            onclick="javascript: var input = this.getElementsByTagName('input')[0]; if (input.checked) {input.checked = false; this.setAttribute('class', '');} else {input.checked = true; this.setAttribute('class', 'selected');}">
            <span style="display: none;">
                <input type="checkbox"
                       name="requestIdentifiers[]"
                       value="<?php echo $users[$i]->id; ?>" />
            </span>
            <!--<img src="components/com_whelpdesk/assets/icons/16-user-silhouette.png" alt="user" />-->
            <?php echo $users[$i]->name; ?> (<?php echo $users[$i]->username; ?>)
        </li>
        <?php endfor; ?>
    </ul>
</form>
