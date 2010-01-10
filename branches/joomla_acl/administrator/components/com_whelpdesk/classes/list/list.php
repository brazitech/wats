<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

wimport('list.column');
wimport('list.filter');
wimport('utilities.paginationstate');

class WList
{
    /**
     *
     * @var array
     */
    protected $_columns = array();

    /**
     *
     * @var JQuery
     */
    protected $_query = null;

    protected $_filters = array();

    protected $_defaultOrder = null;

    protected $_rows = null;

    /**
     *
     * @var int
     */
    protected $_total = null;

    /**
     *
     * @var WPaginationState
     */
    protected $_paginationState = null;

    /**
     *
     * @var string
     */
    protected $_state = null;

    /**
     *
     * @var array
     */
    private static $_instances = array();

    function __construct($options)
    {

    }

    /**
     * Get an instance of WList
     *
     * @param <type> $xml
     * @param <type> $options
     * @return <type> 
     */
    public static function getInstance($xml, $options = array())
    {
        $key = md5(serialize(array($xml, $options)));
        if (array_key_exists($key, self::$_instances))
        {
            return self::$_instances[$key];
        }

        // build new instance.
        self::$_instances[$key] = new WList($options);
        self::$_instances[$key]->load($xml);

        return self::$_instances[$key];
    }

    public function load($xml)
    {
        // Get the XML parser and load the data.
        $parser	= JFactory::getXMLParser('Simple');

        // Attempt to load the XML file.
        if ($parser->loadFile($xml)) {
            // Get the basic attributes
            $this->_state = $parser->document->attributes('state');

            // Check if any columns exist.
            if (isset($parser->document->column))
            {
                // Set the list columns.
                foreach ($parser->document->column as $column)
                {
                    $this->_columns[] = WListColumn::getInstance($column, $this);
                }
            }
            else
            {
                throw new WException('WList XML file must define at least one column.');
            }

            // Get the list query
            if (isset($parser->document->query))
            {
                $db = JFactory::getDBO();
                $query = $parser->document->query;
                $this->_query = new JQuery();

                // add SELECT.
                $columns = array();
                $query = $parser->document->query[0];
                foreach ($query->select as $select)
                {
                    $columns[] = dbName($select->data());
                }
                $this->_query->select($columns);

                // add FROM.
                $tables = array();
                foreach ($query->from as $table)
                {
                    $tables[] = $db->NameQuote($table->data());
                }
                $this->_query->from($tables);

                // add WHERE.
                if (isset($query->where))
                {
                    $conditions = array();
                    foreach ($query->where as $condition)
                    {
                        $conditions[] = $condition->data();
                    }
                    $this->_query->where($conditions);
                }

                // add GROUP BY.
                if (isset($query->group))
                {
                    $grouping = array();
                    foreach ($query->group as $group)
                    {
                        $grouping[] = $group->data();
                    }
                    $this->_query->where($grouping);
                }

                // add ORDER BY.
                $ordering = array();
                foreach ($query->order as $order)
                {
                    $ordering[] = $order->data();
                }
                $this->_query->order($ordering);
            }
            else
            {
                throw new WException('WList XML file must define a query.');
            }

            // Get the filters.
            if (isset($parser->document->filter))
            {
                foreach ($parser->document->filter as $filter)
                {
                    $this->_filters[] = WListFilter::getInstance($filter, $this);
                }
            }

            // Get pagination state
            $this->_paginationState = WPaginationState::getInstance(
                $this->_state,
                $this->getTotal()
            );
        }
        else
        {
            throw new WException('Could not open WList XML file %s.', $xml);
        }
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getFilters()
    {
        return $this->_filters;
    }

    public function getRows()
    {
        // check if rows are already loaded.
        if (isset($this->_rows))
        {
            return $this->_rows;
        }

        // run query to get rows.
        $db = JFactory::getDBO();
        $db->setQuery((string)$this->_query);
        $this->_rows = $db->loadObjectList();

		// Check for an error.
		if ($db->getErrorNum()) {
            throw new WException('WHD_LIST:DATABASE ERROR %s', $db->getErrorMsg());
		}

        return $this->_rows;
    }

    public function getPagination()
    {
        return $this->_paginationState->getPagination();
    }

    public function getTotal()
    {
        // check if rows are already loaded.
        if (isset($this->_total))
        {
            return $this->_total;
        }

        // prepare teh query.
        $query = (string)$this->_query;
        $query = preg_replace('~\s*SELECT.+~', 'SELECT COUNT(*)', $query);

        // run query to get rows.
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $this->_total = (int)$db->loadResult();

		// Check for an error.
		if ($db->getErrorNum()) {
            throw new WException('WHD_LIST:DATABASE ERROR %s', $db->getErrorMsg());
		}

        return $this->_total;
    }

}
