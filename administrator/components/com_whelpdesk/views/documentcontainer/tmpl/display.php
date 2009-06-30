<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JHTML::_('behavior.mootools');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();

$document->addScript('components/com_whelpdesk/assets/javascript/contextmenu/contextmenu.js');
$document->addStyleSheet('components/com_whelpdesk/assets/javascript/contextmenu/contextmenu.css');

/*jimport('joomla.filesystem.folder');

$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'mimetypes' . DS . 'large', '^.*\_.+\.png$');

print_r($files);

foreach ($files as $file) {
    JFile::move(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'mimetypes' . DS . 'large' . DS . $file,
                JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'mimetypes' . DS . 'large' . DS . substr(strstr($file, '_'), 1));
}*/

?>

<script>

window.addEvent('domready', function() {

    // create a context menu for all folder items
	var folderContextMenu = new ContextMenu({
		targets: '#documentcontainers a', // menu only available on document container links
		menu: 'folderContextMenu',
		actions: {
			display: function(element,ref) {
                window.location = element;
			},
            download: function(element,ref) {
                var fileId = element.getAttribute('name').substr(18);
                window.location = 'index.php?option=com_whelpdesk&task=documentcontainer.download&id='+fileId;
            },
            edit: function(element,ref) {
                var fileId = element.getAttribute('name').substr(18);
                window.location = 'index.php?option=com_whelpdesk&task=documentcontainer.edit&id='+fileId;
            },
            del: function(element,ref) {
                var fileId = element.getAttribute('name').substr(18);
                if (confirm('<?php echo str_replace('\'', '\\\'', JText::_('ARE YOU SURE YOU WANT TO DELETE THE DOCUMENT CONTAINER')); ?>')) {
                    window.location = 'index.php?option=com_whelpdesk&task=documentcontainer.delete&id='+fileId;
                }
            },
            move: function(element,ref) {
                var fileId = element.getAttribute('name').substr(18);
                window.location = 'index.php?option=com_whelpdesk&task=documentcontainer.move&id='+fileId;
            }
		},
		offsets: { x:2, y:2 }
	});

	// create a context menu for all file items
	var fileContextMenu = new ContextMenu({
		targets: '#documents a', // menu only available on document links
		menu: 'fileContextMenu',
		actions: {
			display: function(element,ref) {
                element.fireEvent('click');
			},
            download: function(element,ref) {
                var fileId = element.getAttribute('name').substr(9);
                window.location = 'index.php?option=com_whelpdesk&task=document.download&id='+fileId;
            },
            edit: function(element,ref) {
                var fileId = element.getAttribute('name').substr(9);
                window.location = 'index.php?option=com_whelpdesk&task=document.edit&id='+fileId;
            },
            del: function(element,ref) {
                var fileId = element.getAttribute('name').substr(9);
                if (confirm('<?php echo str_replace('\'', '\\\'', JText::_('ARE YOU SURE YOU WANT TO DELETE THE DOCUMENT')); ?>')) {
                    window.location = 'index.php?option=com_whelpdesk&task=document.delete&id='+fileId;
                }
            },
            move: function(element,ref) {
                var fileId = element.getAttribute('name').substr(9);
                window.location = 'index.php?option=com_whelpdesk&task=document.move&id='+fileId;
            }
		},
		offsets: { x:2, y:2 }
	});

    // make sure only one context menu is visible at any time
    folderContextMenu.addEvent('show', function() {fileContextMenu.hide()});
    fileContextMenu.addEvent('show',   function() {folderContextMenu.hide()});

	//sample usages of the enable/disable functionality
	//$('enable').addEvent('click',function(e) { e.stop(); context.enable(); });
	//$('disable').addEvent('click',function(e) { e.stop(); context.disable(); });
	//$('enable-copy').addEvent('click',function(e) { e.stop(); context.enableItem('copy'); });
	//$('disable-copy').addEvent('click',function(e) { e.stop(); context.disableItem('copy'); });

});

</script>

<ul id="folderContextMenu" class="contextmenu">
    <div class="contextmenu-link"></div>
    <li>
        <a href="#display" class="display">
            <?php echo JText::_('Display'); ?>
        </a>
    </li>
	<li class="separator">
        <a href="#edit" class="edit">
            <?php echo JText::_('Edit'); ?>
        </a>
    </li>
	<li>
        <a href="#del" class="del">
            <?php echo JText::_('Delete'); ?>
        </a>
    </li>
    <li>
        <a href="#move" class="move">
            <?php echo JText::_('Move'); ?>
        </a>
    </li>
</ul>

<ul id="fileContextMenu" class="contextmenu">
    <div class="contextmenu-link"></div>
    <li>
        <a href="#display" class="display">
            <?php echo JText::_('Display'); ?>
        </a>
    </li>
	<li>
        <a href="#download" class="download">
            <?php echo JText::_('Download'); ?>
        </a>
    </li>
	<li class="separator">
        <a href="#edit" class="edit">
            <?php echo JText::_('Edit'); ?>
        </a>
    </li>
	<li>
        <a href="#del" class="del">
            <?php echo JText::_('Delete'); ?>
        </a>
    </li>
    <li>
        <a href="#move" class="move">
            <?php echo JText::_('Move'); ?>
        </a>
    </li>
</ul>


<?php WDocumentHelper::render(); ?>

<?php $container = $this->getModel(); ?>
<?php $documentcontainers = $this->getModel('documentcontainers'); ?>
<?php $documents = $this->getModel('documents'); ?>
<?php $parents = $this->getModel('parents'); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="id"     value="<?php echo $container->id; ?>" />
    <input type="hidden" name="parent" value="<?php echo $container->id; ?>" />
    <input type="hidden" name="targetType" value="documentcontainer" />
    <input type="hidden" name="targetIdentifier" value="<?php echo $container->id; ?>" />
    <input type="hidden" name="targetIdentifierAlias" value="<?php echo base64_encode($container->name); ?>" />
    <input type="hidden" name="returnURI" value="<?php echo base64_encode(JRoute::_('index.php?option=com_whelpdesk&task=documentcontainer.display.start&id='.$container->id)); ?>" />
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <?php if (count($documentcontainers) == 0 && count($documents) == 0) : ?>
        <p>
            <?php echo JText::sprintf('DOCUMENT CONTAINER %s IS EMPTY', $container->name); ?>
        </p>
        <?php else : ?>
        <?php if (count($documentcontainers) > 0) : ?>
        <h2 style="margin-top: 0;">Folders</h2>
        <div id="documentcontainers">
            <?php for ($i = 0, $c = count($documentcontainers) ; $i < $c ; $i++) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$documentcontainers[$i]->id); ?>"
               name="documentcontainer-<?php echo $documentcontainers[$i]->id; ?>">
                <div style="float: left;
                            height: 7.8em;
                            width: 7.3em;
                            border: 1px solid #FFFFFF;
                            margin: 1em;
                            text-align: center;
                            padding: 0.5em;
                            overflow: hidden;"
                     onmouseover="javascript: this.style.border = '1px solid #CCCCCC'; this.style.background = '#F3F7FD';"
                     onmouseout="javascript: this.style.border = '1px solid #FFFFFF'; this.style.background = 'none';"
                     class="hasTip"
                     title="<?php echo $documentcontainers[$i]->name . '::' . $documentcontainers[$i]->description; ?>">
                    <img src="components/com_whelpdesk/assets/icons/folder_gray.png"
                         border="0"><br/>
                    <?php echo $documentcontainers[$i]->name; ?>
                </div>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php if (count($documents) > 0) : ?>
        <div style="clear: both;"></div>
        <h2 style="<?php echo (count($documentcontainers) == 0) ? ' margin-top: 0;' : ''; ?>">Files</h2>
        <div id="documents">
            <?php for ($i = 0, $c = count($documents) ; $i < $c ; $i++) : ?>
            <a class="modal"
               rel="{handler: 'iframe', size: {x: 650, y: 375}}"
               name="document-<?php echo $documents[$i]->id; ?>"
               href="<?php echo JRoute::_('index.php?option=com_whelpdesk&modal=1&task=document.display&id='.$documents[$i]->id); ?>">
                <div style="float: left;
                            height: 7.8em;
                            width: 7.3em;
                            margin: 1em;
                            text-align: center;
                            border: 1px solid #FFFFFF;
                            padding: 0.5em;"
                     onmouseover="javascript: this.style.border = '1px solid #CCCCCC'; this.style.background = '#F3F7FD';"
                     onmouseout="javascript: this.style.border = '1px solid #FFFFFF'; this.style.background = 'none';"
                     class="hasTip"
                     title="<?php echo $documents[$i]->filename . '::' . $documents[$i]->description; ?>">
                    <!--<img src="components/com_whelpdesk/assets/mimetypes/<?php echo $documents[$i]->icon; ?>.png"
                         border="0"><br/>-->
                    <img src="components/com_whelpdesk/assets/mimetypes/<?php
                    $icon = strtolower(substr(strrchr($documents[$i]->filename, '.'), 1));
                    if (!JFile::exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'mimetypes' . DS . $icon . '.png')) {
                        $icon = 'unknown';
                    }
                    echo $icon;
                    ?>.png"
                         border="0"
                         style="width: 48px;
                                hieght: 48px;" ><br/>
                    <?php echo $documents[$i]->name; ?>
                </div>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col width-30">
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px;">
            <table class="admintable" style="padding: 0px; margin-bottom: 0px; width: 100%;">
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED BY'); ?></strong>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&cid[]='.$this->getModel('creator')->get('id')); ?>">
                            <?php echo $this->getModel('creator')->get('username'); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo JText::_('CREATED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $container->created,  JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php if ($container->modified != JFactory::getDBO()->getNullDate()) : ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('MODIFIED'); ?></strong>
                    </td>
                    <td>
                        <?php echo JHTML::_('date',  $container->modified, JText::_('DATE_FORMAT_LC2')); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <strong><?php echo JText::_('WEB ADDRESS'); ?></strong>
                    </td>
                    <td>
                        <input onclick="this.select();"
                               value="<?php echo WRoute::_('index.php?option=com_whelpdesk&task=documentcontainer.display&id='.$container->id); ?>"
                               readonly="readonly"
                               id="webAddress"
                               style="width: 100%;"/>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php if (strlen($container->description)) : ?>
        <fieldset class="adminform" style="border: 1px dashed silver; margin: 0px 0px 10px 0px; padding: 10px">
            <?php echo $container->description; ?>
        </fieldset>
        <?php endif; ?>
    </div>
</form>
