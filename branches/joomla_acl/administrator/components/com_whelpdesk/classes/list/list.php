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
wimport('database.query');

class WList
{
    /**
     *
     * @var array
     */
    protected $_columns = array();

    /**
     *
     * @var WQuery
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
    protected $_namespace = null;

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
            $this->_namespace = $parser->document->attributes('state');

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

            // Get the list query.
            if (isset($parser->document->query))
            {
                $db = JFactory::getDBO();
                $query = $parser->document->query[0];
                $this->_query = new WDatabaseQuery();

                if (!$this->_query->fromXML($query))
                {
                    throw new WException('Error parsing WList XML query.');
                }
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

            // Apply the filters to the query.
            foreach($this->getFilters() AS $filter)
            {
                $condition = $filter->getCondition();
                if ($condition !== false)
                {
                    $this->_query->where($condition);
                }
            }

            // Get pagination state.
            $this->_paginationState = WPaginationState::getInstance(
                $this->_namespace,
                $this->getTotal()
            );
        }
        else
        {
            throw new WException('Could not open WList XML file %s.', $xml);
        }
    }

    public function getNamespace()
    {
        return $this->_namespace;
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
        $db->setQuery(
            (string)$this->_query,
            $this->_paginationState->getLimitStart(),
            $this->_paginationState->getLimit()
        );
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

        // prepare the query.
        $query = clone $this->_query;
        $query->resetSelect();
        $query->select('COUNT(*)');

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
