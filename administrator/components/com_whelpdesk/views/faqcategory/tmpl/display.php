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

$document->addScript('components/com_whelpdesk/assets/javascript/php.js/urlencode.js');
$document->addScriptDeclaration("function populateAlias() {if (document.getElementById('alias').value == '') {document.getElementById('alias').value = document.getElementById('name').value.toLowerCase().replace(/(\s+)/g, '-').replace(/([^a-z0-9\-\_\.\(\)])/g, '')}}");

?>

<?php WDocumentHelper::render(); ?>

<?php $category = $this->getModel(); ?>
<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm"
      onsubmit="populateAlias();">

    <!-- request options -->
    <input type="hidden" name="option" value="com_whelpdesk" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="stage"  value="commit" />
    <input type="hidden" name="id"     value="<?php echo $category->id; ?>" />
    <!--<input type="hidden" name="redirect" value="<?php echo $this->redirect;?>" />-->
    <?php echo JHTML::_('form.token'); ?>

    <div class="col width-70">
        <ul>
            <?php $faqs = $this->getModel('faqs'); ?>
            <?php for ($i = 0, $c = count($faqs); $i < $c; $i++) : ?>
            <?php $faq = $faqs[$i]; ?>
            <li>
                <a href="#faq-<?php echo $faq->alias; ?>">
                    <?php echo $faq->question; ?>
                </a>
            </li>
            <?php endfor; ?>
        </ul>
        <?php for ($i = 0, $c = count($faqs); $i < $c; $i++) : ?>
        <?php $faq = $faqs[$i]; ?>
        <a name="faq-<?php echo $faq->alias; ?>">
            <div style="background-color: #F6F6F6;
                        padding: 1em;
                        margin: 1em;">
                <h4 style="margin-top: 0;"><?php echo $faq->question; ?></h4>
                <div><?php echo $faq->answer; ?></div>
            </div>
        </a>
        <?php endfor; ?>
    </div>

    <div class="col width-30">
        <?php echo $this->loadLayout('fieldset_simpledisplay'); ?>
    </div>

</form>
