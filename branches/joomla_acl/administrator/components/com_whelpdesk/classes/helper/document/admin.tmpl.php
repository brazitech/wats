<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

// determine if we should be showing links
$showLinks = (!JRequest::getBool('hidemainmenu', false) && !JRequest::getBool('modal', false));

?>

<div id="wsubheading">
    <h1 id="wsubheading-name" style="margin-top: 0;"><?php echo htmlentities(self::$subtitle, ENT_NOQUOTES, 'UTF-8'); ?></h1>

    <?php if (strlen(self::$description)) : ?>
    <div id="wsubheading-description">
        <p style="margin-top: 0;">
            <?php echo self::$description; ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if (count(self::$pathway)) : ?>
    <div id="wpathway">
        <span class="wpathway-item wpathway-item-home" <?php echo (!$showLinks) ? 'style="color:gray;"' : ''; ?>>
            <?php if ($showLinks) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_whelpdesk'); ?>">
            <?php endif; ?>
                <?php echo JText::_('Helpdesk'); ?>
            <?php if ($showLinks) : ?>
            </a>
            <?php endif; ?>
        </span>
        <?php for ($i = 0, $c = count(self::$pathway); $i < $c ; $i++) : ?>
        <span class="wpathway-item<?php echo ($i == $c - 1) ?  ' wpathway-item-current' : '';?>" <?php echo (!$showLinks) ? 'style="color:gray;"' : ''; ?>>
            &#9658;
            <?php if ($showLinks && strlen(self::$pathway[$i]->link)) : ?>
            <a href="<?php echo JRoute::_(self::$pathway[$i]->link); ?>"
               class="hasTip"
               title="<?php echo htmlentities(self::$pathway[$i]->name, ENT_QUOTES, 'UTF-8'); ?>::<?php echo htmlentities(self::$pathway[$i]->description, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
                <?php echo htmlentities(self::$pathway[$i]->name, ENT_NOQUOTES, 'UTF-8'); ?>
            <?php if ($showLinks) : ?>
            </a>
            <?php endif; ?>
        </span>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- divider -->
<div style="width: 80%;
           height: 1px;
           background-color: #CCCCCC;
           background-image: url(components/com_whelpdesk/assets/subheading-hr.png);
           background-repeat: repeat-y;
           background-position: right;
           margin: 1em -10px;" ></div>
