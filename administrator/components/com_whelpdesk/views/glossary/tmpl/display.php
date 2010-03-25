<?php
/**
 * @version $Id: list.php 146 2009-07-22 18:26:06Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

JFactory::getDocument()->addStyleSheet('components/com_whelpdesk/assets/css/horizontal-list.css');

$list = $this->getModel();
$glossaryItems = $list->getRows();
$filters = $list->getFilters();
$pagination = $list->getPagination();
$char = null;

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="glossary.display" />

    <div id="glossary-navigation">
        <ul class="horizontal-list">
            <?php
            foreach ($glossaryItems AS $glossaryItem):
            $currentChar = JString::substr($glossaryItem->term, 0, 1);
            if ($currentChar != $char) :
            $char = $currentChar;
            ?>
            <li>
                <a href="#glossary-<?php echo $char; ?>">
                    <?php echo $char; ?>
                </a>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <?php
    $char = null;
    foreach ($glossaryItems AS $glossaryItem):
    $currentChar = JString::substr($glossaryItem->term, 0, 1);
    if ($currentChar != $char) :
    echo $char != null ? '</dl>' : '';
    $char = $currentChar;
    ?>
    <h3>
        <a name="glossary-<?php echo $char; ?>">
            <?php echo $char; ?>
        </a>
    </h3>
    <dl>
        <?php
        endif;
        ?>
        <dt>
            <?php echo $glossaryItem->term; ?>
        </dt>
        <dd>
            <?php echo $glossaryItem->description; ?>
            <?php $showSlash = false;
            if (strlen($glossaryItem->field_related_acronyms)) :
            echo '<br/>(';
            foreach (preg_split('~[\,\;\s]+~', $glossaryItem->field_related_acronyms) AS $acronym) :
            echo ($showSlash) ? '/' : '';
            $showSlash = true;
            echo $acronym;
            endforeach;
            echo ')';
            endif;
            ?>
        </dd>
        <?php endforeach; ?>
    </dl>

</form>
