<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

// import the base class
jimport('joomla.database.query');

class WQuery extends JQuery
{
	/**
     * @return WQuery
     */
    public function resetSelect()
	{
		$this->_type = '';
		$this->_select = null;
        return $this;
	}

    /**
     * @return WQuery
     */
    public function resetDelete()
    {
        $this->_type = '';
        $this->_delete = null;
        return $this;
    }

    /**
     * @return WQuery
     */
    public function resetInsert()
    {
        $this->_type = '';
        $this->_insert = null;
        return $this;
    }

    /**
     * @return WQuery
     */
    public function resetUpdate()
    {
        $this->_type = '';
        $this->_update = null;
        return $this;
    }

	/**
     * @return WQuery
     */
	public function resetWhere()
	{
		$this->_where = null;
		return $this;
	}

	/**
     * @return WQuery
     */
	public function resetOrder()
	{
		$this->_order = null;
		return $this;
	}

    /**
     * @todo
     */
    public function toXML()
    {

    }

	/**
     * @todo Does not support all aspects of a WQuery.
     */
    public function fromXML($node)
    {
        // Make sure we are dealing with the correct node.
        if ($node->name() != 'query')
        {
            if (isset($node->query))
            {
                $query = $node->query;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $query = $node;
        }

        // get the database object.
        $db = JFactory::getDBO();

        // add SELECT.
        $columns = array();
        foreach ($query->select as $select)
        {
            $columns[] = dbName($select->data());
        }
        $this->select($columns);

        // add FROM.
        $tables = array();
        foreach ($query->from as $table)
        {
            $tables[] = $db->NameQuote($table->data());
        }
        $this->from($tables);

        // add WHERE.
        if (isset($query->where))
        {
            $conditions = array();
            foreach ($query->where as $condition)
            {
                $conditions[] = $condition->data();
            }
            $this->where($conditions);
        }

        // add GROUP BY.
        if (isset($query->group))
        {
            $grouping = array();
            foreach ($query->group as $group)
            {
                $grouping[] = $group->data();
            }
            $this->where($grouping);
        }

        // add ORDER BY.
        $ordering = array();
        foreach ($query->order as $order)
        {
            $ordering[] = $order->data();
        }
        $this->order($ordering);

        return true;
    }

    /**
     * @todo
     */
    public function fromXML_File($file)
    {

    }
}
