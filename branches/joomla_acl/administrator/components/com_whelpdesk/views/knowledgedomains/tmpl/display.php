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

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="knowledgedomains.display" />
</form>

<?php $knowledgeDomain = $this->getModel(); ?>

<?php
for ($i = 0, $n = count($knowledgeDomain); $i < $n; $i++) :
$knowledgeDomainItem = $knowledgeDomain[$i];
?>
<div style="float: left;
            width: 350px;
            height: 280px;
            border: #AAAAAA 1px solid;
            background-color: #F9F9F9;
            padding: 5px;
            margin: 10px;
            text-align: center;
            background-image: url(components/com_whelpdesk/assets/icons/karbon.png);
            background-position: center center;
            background-repeat: no-repeat;">
    <p>
        <a href="<?php echo 'index.php?option=com_whelpdesk&task=knowledgedomain.display&alias='.$knowledgeDomainItem->alias; ?>">
            <?php echo htmlspecialchars($knowledgeDomainItem->name, ENT_QUOTES); ?>
        </a>
    </p>
    <div>
        <?php echo $knowledgeDomainItem->description; ?>
    </div>
</div>
<?php endfor; ?>
