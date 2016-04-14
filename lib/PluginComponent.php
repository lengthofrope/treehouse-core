<?php

namespace LengthOfRope\Treehouse;

/**
 * The base class all plugin should extend to.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
abstract class PluginComponent extends Component
{

    /**
     * Constructor which loads plugin core.
     *
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public function __construct($coreFile)
    {
        parent::__construct($coreFile);

        // Load plugin/ theme text domain
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
    }

    /**
     * Load the plugin's text-domain
     *
     * @access private
     */
    public function loadTextDomain()
    {
        $pluginBase = plugin_basename(dirname($this->coreFile));
        load_plugin_textdomain($pluginBase, false, $pluginBase . '/languages/');
    }

}
