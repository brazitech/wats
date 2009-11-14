<?php
/**
 * Webamoeba Help Desk
 * 
 * @version		$Id$
 * @package		wats
 * @package		classes
 * @license		GNU/GPL
 */

defined("_JEXEC") or die("");

/**
 * Deals with component specific configuration options. This differs from the
 * Joomla! way in that it separates configuartion options that are defined at
 * menu item and those that are global to the component irrespective of the menu
 * item from which the component was invoked.
 *
 * @author Administrator
 */
final class WConfig extends JObject
{
    /**
     * Object to which handling of the configuration is delegated. Note that
     * unlike normal usage of a JParameter object, we only allow strings to be
     * stored. This makes is easier to store the configuration.
     *
     * @var JParameter
     */
    private $registry = null;

    /**
     * Location of the configuration file
     * 
     * @var String
     */
    private $configFile = null;

    /**
     * @todo Document
     */
    public function __construct() {
        // prepare the delegate
        // note that the data comes later
        $this->registry = new JParameter("");

        // add Element path for custom element types
        $elementPath = JPATH_COMPONENT_ADMINISTRATOR . DS . "elements";
        $this->registry->addElementPath($elementPath);

        // load configuration definition
        $setupFile = JPATH_COMPONENT_ADMINISTRATOR . DS . "componentConfigSetup.xml";
        $this->registry->loadSetupFile($setupFile);

        // load the configuration
        $this->configFile = JPATH_COMPONENT_ADMINISTRATOR . DS . "componentConfig.xml";
        $this->registry->loadFile($this->configFile, "XML", "com_helpdesk");
    }
	
	    /**
     * Sets a value in the config. These values are persisted through out the 
     * session. However, to store these values for future session, the 
     * {@link persist()} method must be invoked.
     *
     * @param String $regpath Path to value in the format abc.xyz.key
     * @param String $value
     * @see persist()
     */
    public function set($regpath, $value) {
        $value = (String)$value;
        // we could return the old value, but why bother, there is no real need
        // for it...
        $this->registry->set($regpath, $value);
    }

    /**
     * Gets a value from the config. If the value is not present the default
     * value is returned.
     *
     * @param String $regpath Path to value in the format abc.xyz.key
     * @param mixed $default
     * @return mixed Value retrieved from the config
     */
    public function get($regpath, $default=null) {
        return $this->registry->get($regpath, $default);
    }

    /**
     * Saves the existing setup to the component specific configuration file
     */
    public function persist() {
        // prepare the persistance data
        $buffer = $this->registry->toString("XML");

        // import the JFile class if has not already been
        if (!class_exists("JFile")) {
            jimport("joomla.filesystem.file");
        }

        // persist the data
        JFile::write($this->configFile, $buffer);
    }

    /**
     * Binds the existing config with an associative array. For security reasons
     * it is recomended that $exitsingKeysOnly is true, unless the source array
     * has already been inspected.
     *
     * @param array $source Associative array
     * @param boolean $exitsingKeysOnly Only bind where values already exist
     */
    public function bind($source, $exitsingKeysOnly=true) {
        // prepare key/keys
        $keys = array_keys($source);
        $key = null;

        // itterate over the source
        for ($i = count($keys) - 1; $i >= 0; $i --) {
            // get the key
            $key = $keys[$i];

            // do not bind if ignoring unknown keys and the key does not exist
            if ($exitsingKeysOnly && ($this->registry->get($key, null) == null)) {
                break 1;
            }

            // bind the value
            $this->setValue($key, $source[$key]);
        }
        // end itterate over the source
    }

    /**
     * Generates an XHTML string that represents the configuration based on the
     * componentConfigSetup.xml file. The various elements are an array named
     * config. For example the element someElement would be named
     * config[someElement].
     *
     * @return String
     * @see JParameter::render()
     */
    public function render() {
        return $this->registry->render("config");
    }

	
	/**
	 * Get the global instance of WConfig
	 * 
	 * @static
	 */
	function &getInstance() {
		static $instance;
		
		if (!$instance) {
			$instance = new WConfig();
		}
		
		return $instance;
	}
}

?>
