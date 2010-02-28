<?php
/**
 * @version		$Id$
 * @package     helpdesk
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
wimport('database.query');;

/**
 *
 *
 */
class JFormFieldRequestHistory extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'RequestHistory';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function _getInput()
	{
        $db = &JFactory::getDbo();
        $query = new WDatabaseQuery();
        $query->select("#__whelpdesk_request_history.*");
        $query->select("#__users.name");
        $query->from("#__whelpdesk_request_history");
        $query->leftJoin("#__users ON #__whelpdesk_request_history.created_by = #__users.id");
        $query->where("#__whelpdesk_request_history.request_id = ".intval($this->value));

        $db->setQuery($query);
        $history =  $db->loadObjectList();

        $html[] = '<table class="admilist" cellspacing="1">';
        $html[] = '<thead>';
        $html[] = '<tr class="row0">';
        $html[] = '<th>description</th>';
        $html[] = '<th>changed by</th>';
        $html[] = '<th>changed</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach($history AS $entry)
        {
            $html[] = '<tr class="row0">';
            $html[] = '<td>'.$entry->description.'</td>';
            $html[] = '<td>'.$entry->name.'</td>';
            $html[] = '<td>'.$entry->created.'</td>';
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';


		return implode("\n", $html);
	}
}