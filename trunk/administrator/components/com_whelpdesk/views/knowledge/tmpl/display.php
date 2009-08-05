<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('helper.knowledge');

JHTML::_('behavior.tooltip');

?>

<?php WDocumentHelper::render(); ?>

<?php $knowledge = $this->getModel(); ?>

<form action="<?php echo JRoute::_('index.php'); ?>"
      method="post"
      name="adminForm">
    <input type="hidden" name="option"     value="com_whelpdesk" />
    <input type="hidden" name="task"       value="" />
    <input type="hidden" name="boxchecked" value="1" />
    <input type="hidden" name="id"         value="<?php echo $knowledge->id; ?>" />
    <?php echo JHTML::_('form.token'); ?>

</form>

<?php $knowledgeRevision = $this->getModel('knowledgeRevision'); ?>

<?php if($knowledgeRevision == null) : ?>
<?php echo JText::_('WHD_KD:THIS IS A NEW PAGE'); ?>
<?php else : ?>

<?php $sections = WKnowledgeHelper::getSections($knowledgeRevision->content, null); ?>

<?php if ($c1 = count($sections)) : ?>
<div id="whelpdesk-toc"
     style="float:right;
            border: #AAAAAA 1px solid;
            background-color: #F9F9F9;
            width: 270px;
            margin: 5px;">
    <h1 style="padding: 10px 10px 0px 10px">
        <?php echo JText::_('WHD KD CONTENTS'); ?>
    </h1>
    <ul style="margin: 0px; padding: 20px;">
        <?php for ($i1 = 0; $i1 < $c1; $i1++) : ?>
        <li>
            <?php echo $sections[$i1]['section']; ?>
            <?php if ($c2 = count($sections[$i1]['children'])) : ?>
            <ul>
                <?php for ($i2 = 0; $i2 < $c2; $i2++) : ?>
                <li>
                    <?php echo $sections[$i1]['children'][$i2]['section']; ?>
                    <?php if ($c3 = count($sections[$i1]['children'][$i2]['children'])) : ?>
                    <ul>
                        <?php for ($i3 = 0; $i3 < $c3; $i3++) : ?>
                        <li>
                            <?php echo $sections[$i1]['children'][$i2]['children'][$i3]['section']; ?>
                        </li>
                        <?php endfor; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endfor; ?>
            </ul>
            <?php endif; ?>
        </li>
        <?php endfor; ?>
    </ul>
</div>
<?php endif; ?>

<?php echo WKnowledgeHelper::parse($this->getModel('knowledgedomain')->id, $knowledgeRevision->content); ?>

<?php endif; ?>

