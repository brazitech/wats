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

?>

<?php WDocumentHelper::render(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="glossary.display" />

    <div id="glossary-navigation">
        <ul class="horizontal-list">
            <?php
            $glossaryItems = $this->getModel();
            $current = '';
            for ($i = 0, $n = count($glossaryItems); $i < $n; $i++) :
            $glossaryItem = $glossaryItems[$i];
            $startChar = JString::str_split($glossaryItem->term);
            $startChar = $startChar[0];
            if ($current != $startChar) :
            $current = $startChar;
            ?>
            <li>
                <a href="#glossary-<?php echo $current; ?>"><?php echo $current; ?></a> |
            </li>
            <?php endif; ?>
            <?php endfor; ?> 
        </ul>
    </div>

    <div>
        
    </div>

    <?php
    $glossaryItems = $this->getModel();
    $current = '';
    for ($i = 0, $n = count($glossaryItems); $i < $n; $i++) :
    $glossaryItem = $glossaryItems[$i];
    ?>
    <?php
    $startChar = JString::str_split($glossaryItem->term);
    $startChar = $startChar[0];
    if ($current != $startChar) :
    $current = $startChar;
    ?>
    <h2>
        <a name="glossary-<?php echo $current; ?>">
            <?php echo $current; ?>
        </a>
    </h2>
    <?php endif; ?>
    <div>
        <h4>
            <?php echo $glossaryItem->term; ?>
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
        </h4>
        <p>
            <?php echo $glossaryItem->description; ?>
        </p>
    </div>
    <?php endfor; ?>

</form>
