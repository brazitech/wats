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
    protected $columns = array();

    /**
     *
     * @var WQuery
     */
    protected $query = null;

    protected $filters = array();

    protected $defaultOrder = null;

    protected $rows = null;

    protected $rowPointer = null;

    /**
     *
     * @var int
     */
    protected $total = null;

    /**
     *
     * @var WPaginationState
     */
    protected $paginationState = null;

    /**
     *
     * @var string
     */
    protected $namespace = null;

    /**
     *
     * @var array
     */
    private static $instances = array();

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
        if (array_key_exists($key, self::$instances))
        {
            return self::$instances[$key];
        }

        // build new instance.
        self::$instances[$key] = new WList($options);
        self::$instances[$key]->load($xml);

        return self::$instances[$key];
    }

    public function load($xml, $params = array())
    {
        // Get the XML parser and load the data.
        $parser	= JFactory::getXMLParser('Simple');

        // Attempt to load the XML file.
        if ($parser->loadFile($xml)) {
            // Get the basic attributes
            $this->namespace = $parser->document->attributes('state');

            // Check if any columns exist.
            if (isset($parser->document->column))
            {
                // Set the list columns.
                foreach ($parser->document->column as $column)
                {
                    $this->columns[] = WListColumn::getInstance($column, $this);
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
                $this->query = new WDatabaseQuery();

                if (!$this->query->fromXML($query))
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
                    $this->filters[] = WListFilter::getInstance($filter, $this);
                }
            }

            // Apply the filters to the query.
            foreach($this->getFilters() AS $filter)
            {
                $condition = $filter->getCondition();
                if ($condition !== false)
                {
                    $this->query->where($condition);
                }
            }

            // Get pagination state.
            $this->paginationState = WPaginationState::getInstance(
                $this->namespace,
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
        return $this->namespace;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumn($name)
    {
        foreach ($this->columns as $column)
        {
            if ($column->getName() == $name)
            {
                return $column;
            }
        }
        return null;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getRows()
    {
        // check if rows are already loaded.
        if (isset($this->rows))
        {
            return $this->rows;
        }

        // run query to get rows.
        $db = JFactory::getDBO();
        $db->setQuery(
            (string)$this->query,
            $this->paginationState->getLimitStart(),
            $this->paginationState->getLimit()
        );
        $this->rows = $db->loadObjectList();

		// Check for an error.
		if ($db->getErrorNum()) {
            throw new WException('WHD_LIST:DATABASE ERROR %s', $db->getErrorMsg());
		}

        return $this->rows;
    }

    /**
     * Gets the row from the specified position
     * 
     * @param int $rowNumber
     * @return Object
     */
    public function getRow($rowNumber)
    {
        $this->getRows();

        if (count($this->rows) < ($rowNumber + 1))
        {
            return null;
        }

        return $this->rows[$rowNumber];
    }

    /**
     * Gets the current row according to the internal row pointer.
     *
     * @return Object
     */
    public function getCurrentRow()
    {
        if ($this->rowPointer == null)
        {
            $this->rowPointer = 0;
        }

        return $this->getRow($this->rowPointer);
    }

    /**
     * Gets the internal row pointer
     *
     * @return int
     */
    public function getRowPointer()
    {
        return $this->rowPointer;
    }

    public function nextRow()
    {
        // set the row pointer
        if (is_null($this->rowPointer))
        {
            $this->rowPointer = 0;
        }
        else
        {
            $this->rowPointer++;
        }

        return $this->rows[$this->rowPointer];
    }

    public function getPagination()
    {
        return $this->paginationState->getPagination();
    }
    
    public function setPaginationState(WPaginationState $paginationState)
    {
        $this->paginationState = $paginationState;
    }

    public function getTotal()
    {
        // check if rows are already loaded.
        if (isset($this->total))
        {
            return $this->total;
        }

        // prepare the query.
        $query = clone $this->query;
        $query->resetSelect();
        $query->select('COUNT(*)');

        // run query to get rows.
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $this->total = (int)$db->loadResult();

		// Check for an error.
		if ($db->getErrorNum())
        {
            throw new WException('WHD_LIST:DATABASE ERROR %s', $db->getErrorMsg());
		}

        return $this->total;
    }

}
