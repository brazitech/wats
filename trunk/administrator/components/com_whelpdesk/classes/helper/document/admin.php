<?php
/**
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

JFactory::getDocument()->addStyleDeclaration('.icon-48-whelpdesk {
background-image: url(components/com_whelpdesk/assets/title.png);
}');

/**
 * Interface that all implementing classes must implement. This is used to 
 * create a generic class interface for all WToolbarHelper implementations.
 */
abstract class WDocumentHelper implements WDocumentHelperInterface {

    private static $subtitle = 'Webamoeba Help Desk';
    private static $description = '';
    private static $pathway = array();

    public static function title($title, $icon = 'whelpdesk') {
		JToolbarHelper::title('&nbsp;<!--'.$title.'-->', $icon);
	}

    public function subtitle($subtitle) {
        self::$subtitle = (string)$subtitle;
    }

    public function description($description) {
        self::$description = (string)$description;
    }

    public function addPathwayItem($name, $description=null, $link=null) {
        // create item
        $item = new stdClass();
        $item->name = $name;
        $item->description = $description;
        $item->link = $link;

        // add to pathway
        self::$pathway[] = $item;
    }

    public function render() {

        $document = JFactory::getDocument();
        $documentTitle = $document->getTitle();
        $documentTitle .= ' - Webamoeba Help Desk - ' . self::$subtitle;
        $document->setTitle($documentTitle);

        include('admin.tmpl.php');
    }
}
