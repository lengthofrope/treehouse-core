<?php

namespace LengthOfRope\Treehouse;

/**
 * The base class all plugin and themes should extend to.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
abstract class Component
{

    private $coreFile;

    /**
     * Constructor which loads plugin core.
     *
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public function __construct($coreFile)
    {
        $this->coreFile = $coreFile;

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
        $pluginBase = basename(dirname($this->coreFile));
        load_plugin_textdomain($pluginBase, false, $pluginBase . '/languages');
    }

}
