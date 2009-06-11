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
class WDocumentHelper implements WDocumentHelperInterface {

    private static $subtitle = 'Webamoeba Help Desk';
    private static $description = '';
    private static $pathway = array();

    public static function title($title, $icon = 'whelpdesk') {
		JToolbarHelper::title($title, $icon);
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
        include('admin.tmpl.php');
    }
}
