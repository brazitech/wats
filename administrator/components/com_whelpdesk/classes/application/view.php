<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package helpdesk
 */

// No direct access
defined('JPATH_BASE') or die();

abstract class WView {

    /**
     * Instances of views, we always reuse view objects, no need to create more
     * than we need!
     * 
     * @var WView[][]
     */
    private static $instances = array();

    /**
     * Array of models that are associated with the view.
     *
     * @var mixed[]
     */
    private $models = array();


	/**
	 * The default model
	 *
	 * @var	mixed
	 */
	private $defaultModel;

    /**
	 * Array of paths where layouts exist. Stored in order of priority, the
     * higher the index value the higher the priority.
	 *
	 * @var	String[]
	 */
	public $layoutPaths = array();

	/**
	 * Name of the layout
	 *
	 * @var String
	 */
	private $layout = 'default';

	/**
	 * Constructor
	 */
	public function __construct() {
        // add the generic templates path
        $this->addLayoutPath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'tmpl');
	}

    /**
     * Adds a model to the view. If the name of the model is already assigned,
     * it will be replaced. If the model has already been added under a
     * different alias, the model will still be added.
     *
     * @param String $name
     * @param mixed  $model
     */
	public function addModel($name, $model, $default = false) {
		if ($default) {
            $this->defaultModel = $model;
        }
        
        $this->models[$name] = $model;
	}

	/**
	 * Get a model assigned to the view
	 *
	 * @param String $name The name of the model (optional)
	 * @return mixed model
	 */
	public function getModel($name = '', $default=null) {
		$name = (string)$name;

        if ($name === '') {
			return $this->defaultModel;
		}

        // send the model back if we know it
        if (array_key_exists($name, $this->models)) {
            return $this->models[(string)$name];
        }

        // send the default value, we don't know this model
		return $default;
	}

	/**
	 * Get the name of the layout.
	 *
	 * @return string Layout name
	 */
	public function getLayout() {
		return $this->layout;
	}


	/**
	 * Sets the layout name to use
	 *
	 * @param	string $template The template name.
	 * @return	string Previous value
	 */

	public function setLayout($layout) {
		$this->layout = (string)$layout;
	}


	/**
	 * Adds a new path in which to look for layouts. Paths must be added in
     * reverse order of priority.
	 *
	 * @param string Path to layout folder
     * @todo add security
	 */
	public function addLayoutPath($path) {
        $this->layoutPaths[] = (string)$path;
	}

    /**
     * Renders and outputs the view.
     */
    public function render() {
        // load the layout!
        $layout = (JRequest::getCmd('layout')) ? JRequest::getCmd('layout') : $this->getLayout();
        echo $this->loadLayout($layout);
    }

	/**
	 * Load the layout and capture the output.
	 *
	 * @param String $layout Name of the layout to load
	 * @return String Output of the template
	 */
	protected function loadLayout($layout = null) {
		// determine layout and file
        $layout = (empty($layout)) ? $this->getLayout() : (string)$layout;
		$file   = $layout . '.php';

		// find the layout file to load
		jimport('joomla.filesystem.path');
		$layoutPath = JPath::find($this->layoutPaths, $file);

        // check that there is a layout to load
        if ($layoutPath === false) {
            throw new WException('UNKNOWN LAYOUT %s', $file);
        }

        // capture the layout output
        ob_start();
        require_once $layoutPath;
        $output = ob_get_contents();
        ob_end_clean();

        // all done!
        return $output;
	}

    /**
     * Resets the object back to it's original state.
     */
    public function reset() {
        $this->models       = array();
        $this->defaultModel = null;
        $this->layout       = 'default';
    }

    protected function pagination() {
        // create JPagination object
        if (!class_exists('JPagination')) {
            jimport('joomla.html.pagination');
        }
        $this->addModel('pagination', new JPagination($this->getModel('paginationTotal'),
                                                      $this->getModel('paginationLimitStart'),
                                                      $this->getModel('paginationLimit')));
    }


    /**
     * Gets a WView object. Remember that WView objects are cached, and
     * therefore state data is not reset. This could be problematic when resuing
     * complex WView objects.
     *
     * @see WView::reset()
     * @param String $entity
     * @param String $view
     * @param String $format
     * @return WView
     */
    static public function getInstance($entity, $view, $format = null) {
        // determine format
        if ($format === null) {
            $format = JRequest::getCmd('format', 'html');
        }

        // lowercase all round
        $entity = strtolower($entity);
        $view   = strtolower($view);
        $format = strtolower($format);

        // check if we need to create the view
        if (empty(self::$instances[$entity][$view][$format])) {
            // determine path, file and class name
            $viewpath = JPATH_COMPONENT . DS . 'views' . DS . $entity . DS;
            $viewFile = $view . '.' . $format . '.php';
            $viewClass = ucfirst($entity) . strtoupper($format) . 'WView';

            // load the file
            require_once($viewpath . $viewFile);

            // create the new view object and add the layout paths
            $viewObject = new $viewClass();
            $viewObject->addLayoutPath(JPATH_COMPONENT . DS . 'views' . DS . $entity . DS . 'tmpl');

            // cache the view object
            self::$instances[$entity][$view][$format] = $viewObject;
        }

        // all done, send the object back!
        return self::$instances[$entity][$view][$format];
    }

}

?>
