<?php
/**
 * @version $Id: list.php 236 2010-04-03 14:49:25Z webamoeba $
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

$rootCategory = $this->getModel();

$style = <<<STYLE
.requestCategory
{
    margin-bottom: 1em;
    margin-top: 0.5em;
}

.requestCategory .requestCategoryDetails
{
}

.requestCategory .requestCategoryDetails a.requestCategorySelector
{
    font-size: 200%;
}

.requestCategory .requestCategoryChildren
{
    margin-left: 2em;
}
STYLE;

$document = &JFactory::getDocument();
$document->addStyleDeclaration($style);

class WTmplHelperSelectRequestCategory
{
    static public function renderChildren($category)
    {
        if (is_array($category->children))
        {
            foreach ($category->children as $child)
            {
                self::renderCategory($child);
            }
        }
    }

    static public function renderCategory($category)
    {
?>
<div class="requestCategory" id="requestCategory-<?php echo $category->id; ?>">
    <div class="requestCategoryDetails">
        <a class="requestCategorySelector" href="<?php echo JRoute::_('index.php?option=com_whelpdesk&task=request.new.details&category_id='.$category->id); ?>">
            <?php echo $category->name; ?>
        </a>
        <?php if (strlen($category->description)): ?>
        <div class="description">
            <?php echo $category->description; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="requestCategoryChildren">
        <?php self::renderChildren($category); ?>
    </div>
</div>
<?php
    }
}

?>

<?php WDocumentHelper::render(); ?>

<div class="whdInformationText">
    <?php echo JText::_('WHD_R:NEW:SELECT CATEGORY INFORMATION TEXT'); ?>
</div>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm">

    <!-- request options -->
    <input type="hidden" name="option"       value="com_whelpdesk" />
    <input type="hidden" name="task"         value="<?php echo WFactory::getCommand(); ?>" />
    <input type="hidden" name="boxchecked"   value="0" />
    <input type="hidden" name="limit"        value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $filters1['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filters1['orderDirection']; ?>" />
    <?php echo JHTML::_('form.token'); ?>


    <div class="whdRequestCategories">
        <?php WTmplHelperSelectRequestCategory::renderChildren($rootCategory)?>
    </div>

</form>
