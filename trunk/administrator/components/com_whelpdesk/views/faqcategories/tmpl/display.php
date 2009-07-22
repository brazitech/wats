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

<?php $categories = $this->getModel(); ?>

<?php
for ($i = 0, $n = count($categories); $i < $n; $i++) :
$category = $categories[$i];
?>
<div style="float: left;
            width: 350px;
            height: 280px;
            border: #AAAAAA 1px solid;
            background-color: #F9F9F9;
            padding: 5px;
            margin: 10px;
            text-align: center;
            background-image: url(components/com_whelpdesk/assets/icons/kmenu_a.png);
            background-position: center center;
            background-repeat: no-repeat;">
    <p>
        <a href="<?php echo 'index.php?option=com_whelpdesk&task=faqcategory.display&id='.$category->id; ?>">
            <?php echo htmlspecialchars($category->name, ENT_QUOTES); ?>
        </a>
    </p>
    <div>
        <?php echo $category->description; ?>
    </div>
</div>
<?php endfor; ?>
