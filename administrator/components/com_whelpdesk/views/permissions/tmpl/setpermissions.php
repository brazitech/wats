<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.modal');

?>

<?php
// add javascript

function flattenControlTree($controlTree, $includeChildren=true) {
    $flat = array();

    foreach($controlTree AS $control) {
        $flatItem = new stdclass();
        $flatItem->type       = $control->type;
        $flatItem->identifier = $control->identifier;
        $flat[] = $flatItem;

        if (@$control->children) {
            $flat = array_merge($flat, flattenControlTree($control->children));
            if ($includeChildren) {
                $flatItem->children = flattenControlTree($control->children, false);
            }
        }
    }

    return $flat;
}

JHtml::_('behavior.mootools');

$controlTree = $this->getModel();

$scriptControlTree = flattenControlTree($controlTree);

$script = 'accessTree = ' . @json_encode($controlTree) . ';';
$script .= <<<SCRIPT

SCRIPT;
JFactory::getDocument()->addScriptDeclaration($script);

?>

<?php $users = $this->getModel('users'); ?>

<script>

    function updatePermissionSelectBoxes(selectBoxes) {
        for (var i = 0; i < selectBoxes.length; i++) {
            var node = selectBoxes[i];
            var userControl = document.getElementById('permissions-' + node.type + '-' + node.identifier);

            if (node.children.length > 0) {
                updatePermissionSelectBoxesState(node.children, (userControl.value == '-1' || userControl.disabled));
            }
            updatePermissionSelectBoxes(node.children);
        }
    }

    function updatePermissionSelectBoxesState(selectBoxes, disabled) {
        for (var i = 0; i < selectBoxes.length; i++) {
            var node = selectBoxes[i];
            var userControl = document.getElementById('permissions-' + node.type + '-' + node.identifier);

            userControl.disabled = disabled;

            if (node.children.length > 0) {
                updatePermissionSelectBoxesState(node.children, disabled);
            }
        }
    }
</script>

<script>

    currentRequestId = 1;
    totalRequestColumns = <?php echo count($users); ?>

    function hideRequestColumn(id, accessControls) {
        for (var i = 0; i < accessControls.length; i++) {
            var node = accessControls[i];
            var tdId = 'request-' + id + '-' + node.type + '-' + node.identifier;
            document.getElementById(tdId).style.display = 'none';

            hideRequestColumn(id, node.children);
        }
    }

    function showRequestColumn(id, accessControls) {
        for (var i = 0; i < accessControls.length; i++) {
            var node = accessControls[i];
            var tdId = 'request-' + id + '-' + node.type + '-' + node.identifier;
            document.getElementById(tdId).style.display = '';

            showRequestColumn(id, node.children);
        }
    }

    function nextRequestColumn() {
        if (currentRequestId + 1 >= totalRequestColumns) {
            return;
        }

        document.getElementById('request-' + currentRequestId).style.display = 'none';
        document.getElementById('request-' + (currentRequestId + 2)).style.display = '';

        hideRequestColumn(currentRequestId, accessTree);
        showRequestColumn((currentRequestId + 2), accessTree);

        currentRequestId = currentRequestId + 1;

        if (currentRequestId + 1 >= totalRequestColumns) {
            document.getElementById('nextColumnButton').style.visibility = 'hidden';
        }

        if (currentRequestId > 1) {
            document.getElementById('previousColumnButton').style.visibility = '';
        }

        // redraw
        document.getElementById('permissionsTable').style.display = 'none';
        document.getElementById('permissionsTable').style.display = '';
    }

    function previousRequestColumn() {
        if (currentRequestId == 1) {
            return;
        }

        document.getElementById('request-' + (currentRequestId + 1)).style.display = 'none';
        document.getElementById('request-' + (currentRequestId - 1)).style.display = '';

        hideRequestColumn((currentRequestId + 1), accessTree);
        showRequestColumn((currentRequestId - 1), accessTree);

        currentRequestId = currentRequestId - 1;

        if (currentRequestId == 1) {
            document.getElementById('previousColumnButton').style.visibility = 'hidden';
        }

        if (currentRequestId + 1 < totalRequestColumns) {
            document.getElementById('nextColumnButton').style.visibility = '';
        }

        // redraw
        document.getElementById('permissionsTable').style.display = 'none';
        document.getElementById('permissionsTable').style.display = '';
    }

</script>

<script>
    function loadUserPermission(id, requestType, requestIdentifier, type, control) {
        var req = new Request({
            method: 'get',
            url: 'index.php',
            onRequest: function() {
            },
            data: {
                'option' : 'com_whelpdesk',
                'task'   : 'permissions.status',
                'format' : 'json',
                'targetType' : '<?php echo $this->getModel('targetType'); ?>',
                'targetIdentifier' : '<?php echo $this->getModel('targetIdentifier'); ?>',
                'requestType' : requestType,
                'requestIdentifier' : requestIdentifier,
                'controlType' : type,
                'control' : control
            },
            onComplete: function(response) {
                response = eval('(' + trim(response) + ')');
                var ui = document.getElementById('request-object-' + response.requestIdentifier + '-' + response.controlType + '-' + response.control);

                var icon = null;
                switch (response.rule) {
                    case 'allow':
                        icon = 'components/com_whelpdesk/assets/icons/lock-unlock.png';
                        ui.innerHTML = '<img src="' + icon + '" alt="<?php echo JText::_('ALLOW'); ?>" title="<?php echo JText::_('ALLOW'); ?>" />';
                        if (!response.access) {
                            ui.innerHTML += '&nbsp;';
                            ui.innerHTML += '<img src="components/com_whelpdesk/assets/icons/exclamation.png" alt="<?php echo JText::_('NOT ALLOWED'); ?>" class="hasTip" title="Cannot allow access"/>';
                        }
                        break;
                    case 'deny':
                        icon = 'components/com_whelpdesk/assets/icons/lock.png';
                        ui.innerHTML = '<img src="' + icon + '" alt="<?php echo JText::_('DENY'); ?>" title="<?php echo JText::_('DENY'); ?>" />';
                        break;
                    default:
                        icon = 'components/com_whelpdesk/assets/icons/lock-disable.png';
                        ui.innerHTML = '<img src="' + icon + '" alt="<?php echo JText::_('INHERIT'); ?>" title="<?php echo JText::_('INHERIT'); ?>" />';
                        ui.innerHTML += '&nbsp;';
                        if (response.access) {
                            icon = 'components/com_whelpdesk/assets/icons/lock-unlock.png';
                            ui.innerHTML += '<img src="' + icon + '" alt="<?php echo JText::_('ALLOW'); ?>" title="<?php echo JText::_('ALLOW'); ?>" />';
                        } else {
                            icon = 'components/com_whelpdesk/assets/icons/lock.png';
                            ui.innerHTML += '<img src="' + icon + '" alt="<?php echo JText::_('DENY'); ?>" title="<?php echo JText::_('DENY'); ?>" />';
                        }
                        ui.innerHTML += '&nbsp;';
                        icon = 'components/com_whelpdesk/assets/icons/information-frame.png';
                        ui.innerHTML += '<a class="modal" href="http://www.google.com" href="http:www.google.com" rel="{handler: \'iframe\', size: {x: 500, y: 400}}">'
                                     +  '<img src="' + icon + '" alt="<?php echo JText::_('TRACE'); ?>" title="<?php echo JText::_('TRACE'); ?>" />'
                                     +  '</a>';
                        break;
                }
            }
        }).send();
    }
</script>



<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"           value="com_whelpdesk" />
    <input type="hidden" name="task"             value=""/>
    <input type="hidden" name="targetIdentifier" value="<?php echo $this->getModel('targetIdentifier'); ?>" />
    <input type="hidden" name="targetType"       value="<?php echo $this->getModel('targetType'); ?>" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode($this->getModel('targetIdentifierAlias')); ?>" />
    <input type="hidden" name="returnURI"        value="<?php echo base64_encode($this->getModel('returnURI')); ?>" />
    <input type="hidden" name="requestType"      value="user" />
    <?php for ($n = 0, $t = count($users); $n < $t; $n++) : ?>
    <input type="hidden" name="requestIdentifiers[]" value="<?php echo $users[$n]->id; ?>" />
    <?php endfor; ?>
    <?php echo JHTML::_('form.token'); ?>

    <table class="adminlist" cellspacing="1" id="permissionsTable">
        <thead>
            <tr>
                <th class="title" rowspan="2">
                    <?php echo JText::_('CONTROL'); ?>
                </th>
                <th class="title" 
                    colspan="<?php echo (count($users) > 1) ? '2' : '1'; ?>"
                    style="width: 240px;">
                    <a style="cursor: pointer;
                              visibility: hidden;"
                       onclick="javascript: previousRequestColumn();"
                       id="previousColumnButton">
                        &#9668;
                    </a>
                    <?php echo JText::_('USERS'); ?>
                    <a style="cursor: pointer;
                              <?php echo (count($users) <= 2) ? 'visibility: hidden;' : ''; ?>"
                       onclick="javascript: nextRequestColumn();"
                       id="nextColumnButton">
                        &#9658;
                    </a>
                </th>
                <th class="title" 
                    rowspan="2"
                    style="width: 150px;">
                    <?php echo JText::_('ACCESS STATUS'); ?>
                </th>
            </tr>
            <tr>
                <?php for ($i = 0, $c = count($users); $i < $c; $i++) : ?>
                <th class="title" 
                    id="request-<?php echo $i + 1; ?>"
                    style="display: <?php echo ($i > 1) ? 'none' : ''; ?>;
                           width: <?php echo (count($users) > 1) ? '120' : '240'; ?>px;">
                    <?php echo $users[$i]->name; ?><br/>
                    (<?php echo $users[$i]->username; ?>)<br/>
                </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            function showControlTableRows($controls, $users, $targetType, $canChange = false, $depth = 0) {
            for ($i = 0, $c = count($controls); $i < $c; $i++) :
            $control = $controls[$i];
            //$canChange = ($canChange || $targetType == $control->type);
            ?> 
            <tr class="row<?php echo ($GLOBALS['WHD_SETPERMISSIONS_ROW']++ % 2); ?>">
                <td style="padding-left: <?php echo $depth * 2 + 1?>em;">
                    <?php echo (count($control->children)) ? '&#9698;' : ''; ?>
                    <!--<?php echo $control->type; ?> - <?php echo $control->identifier; ?>-->
                    <?php echo $control->description; ?>
                </td>
                <?php for ($n = 0, $t = count($users); $n < $t; $n++) : ?> 
                <td align="center"
                    id="request-<?php echo $n + 1; ?>-<?php echo $control->type; ?>-<?php echo $control->identifier; ?>"
                    style="display: <?php echo ($n > 1) ? 'none' : ''; ?>;
                           padding: 0;
                           margin: 0;">
                    <div id="request-object-<?php echo $users[$n]->id; ?>-<?php echo $control->type; ?>-<?php echo $control->identifier; ?>"
                         style="hieght: 100%;
                                width: 100%;">
                        <?php if ($canChange || $targetType == $control->type) : ?>
                        <img src="components/com_whelpdesk/assets/icons/16-arrow-circle-315.png"
                             alt="<?php JText::_('LOAD USERS CURRENT PERMISSIONS'); ?>"
                             onclick="javascript: this.setAttribute('src', 'components/com_whelpdesk/assets/javascript/ajax-circle.gif');
                                                  loadUserPermission(<?php echo $n; ?>,
                                                                     'user',
                                                                     '<?php echo $users[$n]->id; ?>',
                                                                     '<?php echo $control->type; ?>',
                                                                     '<?php echo $control->identifier; ?>');"
                             style="cursor: pointer;"/>
                        <?php endif; ?>
                    </div>
                </td>
                <?php endfor; ?> 
                <td align="center"
                    style="width: 150px;">
                    <select name="permissions[<?php echo $control->type; ?>][<?php echo $control->identifier; ?>]"
                            id="permissions-<?php echo $control->type; ?>-<?php echo $control->identifier; ?>"
                            onchange="javascript: updatePermissionSelectBoxes(accessTree);"
                            style="<?php echo ($canChange || $targetType == $control->type) ? '' : 'display: none;'; ?>">
                        <option value=""><!-- Unknown... --></option>

                        margin: 2px; background-image: url(components/com_whelpdesk/assets/flags/de.png); background-repeat: no-repeat; padding-left: 24px;

                        <option value="0"
                                style="margin: 2px;
                                       background-image: url(components/com_whelpdesk/assets/icons/lock-disable.png);
                                       background-repeat: no-repeat;
                                       padding-left: 24px;">
                            <?php echo JText::_('Inherit'); ?>
                        </option>
                        <option value="+1"
                                style="margin: 2px;
                                       background-image: url(components/com_whelpdesk/assets/icons/lock-unlock.png);
                                       background-repeat: no-repeat;
                                       padding-left: 24px;">
                            <?php echo JText::_('Allow'); ?>
                        </option>
                        <option value="-1"
                                style="margin: 2px;
                                       background-image: url(components/com_whelpdesk/assets/icons/lock.png);
                                       background-repeat: no-repeat;
                                       padding-left: 24px;">
                            <?php echo JText::_('Deny'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <?php if (array_key_exists('children', get_object_vars($control))) { showControlTableRows($control->children, $users, $targetType, ($canChange || $targetType == $control->type), $depth + 1); } ?>
            <?php endfor; ?>
            <?php } ?>
            <?php $GLOBALS['WHD_SETPERMISSIONS_ROW'] = 0 ?>
            <?php showControlTableRows($this->getModel(), $users, $this->getModel('targetType')); ?>
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
                    <img height="16" border="0" width="16" alt="Pending" src="components/com_whelpdesk/assets/icons/lock-disable.png"/>
                </td>
                <td style="border-right: 1px solid #AAAAAA;">
                    <?php echo JText::_('Inherit (no rules definied)'); ?>
                </td>
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
                <td style="border-right: 1px solid #AAAAAA;">
                    <?php echo JText::_('Denied (although an allow rule exists, there is a deny rule further up the tree)'); ?>
                </td>
                <td>
                    <img height="16" border="0" width="16" alt="Pending" src="components/com_whelpdesk/assets/icons/information-frame.png"/>
                </td>
                <td>
                    <?php echo JText::_('Trace the rules'); ?>
                </td>
            </tr>
		</tbody>
    </table>

</form>
