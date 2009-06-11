<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

/**
 * Interface that all implementing classes must implement. This is used to 
 * create a generic class interface for all WToolbarHelper implementations.
 */
class WDocumentHelper {

    private static $heading = 'Webamoeba Help Desk';
    private static $description = '';
    private static $pathway = array();

    public static function title($title, $icon = 'whelpdesk') {
		JToolbarHelper::title($title, $icon);
	}

    public function setHeading($heading) {
        $this->heading = (string)$heading;
    }

    public function setDescription($description) {
        $this->description = (string)$description;
    }

    public function addItem($name, $description=null, $link=null) {
        // create item
        $item = new stdClass();
        $item->name = $name;
        $item->description = $description;
        $item->link = $link;

        // add to pathway
        $this->pathway[] = $item;
    }

    public function render() {
        echo '<div id="wsubheading">';
        echo '<h1 id="wsubheadingname">' . htmlentities($this->name, ENT_NOQUOTES, 'UTF-8') . '</h1>';

        if (strlen($this->description)) {
            echo '<div id="wsubheadingdescription">' . $this->description . '</div>';
        }

        echo '</div>';

        echo '<div id="documentpath">';
        echo '<span class="documentcontainer documentcontainerhome">';
        echo '    <a href="' . JRoute::_('index.php?option=com_whelpdesk') . '>">';
        echo JText::_('Helpdesk');
        echo '    </a>';
        echo '</span>';
        for ($i = 0, $c = count($this->pathway) ; $i < $c ; $i++) {
            echo '<span class="documentcontainer">';
            echo '    &#9658;';
            echo '    <a href="' . JRoute::_($this->pathway[$i]->url) . '"';
            echo '       class="hasTip"';
            echo '       title="' . $this->pathway[$i]->name . '::' . $this->pathway[$i]->description . '">';
            echo $this->pathway[$i]->name;
            echo '    </a>';
            echo '</span>';
        }
        echo '<span class="documentcontainer currentdocumentcontainer">';
        echo '    &#9658;';
        echo $container->name;
        echo '</span>';
        echo '</div>';

        // add divider
        echo '<div style="width: 80%;
                   height: 1px;
                   background-color: #CCCCCC;
                   background-image: url(components/com_whelpdesk/assets/subheading-hr.png);
                   background-repeat: repeat-y;
                   background-position: right;
                   margin: 1em -10px;" ></div>';
    }

}
