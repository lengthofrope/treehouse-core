<?php

namespace LengthOfRope\Treehouse\Utils;

/**
 * All Treehouse plugins and themes depend on the Treehouse CORE plugin since
 * this plugin houses all shared code. This class makes sure we load the 
 * Treehouse CORE plugin before any other Treehouse plugin without the need of
 * converting to a mu-plugin since they are less flexible and can only be 
 * manually updated.
 * 
 * Note: themes are loaded at a later stage by default.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class PluginLoadPriority
{

    private $pluginFile;

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;

        // Add filters which make sure Treehouse CORE is always loaded before other Treehouse plugins
        add_filter('pre_update_option_active_plugins', array($this, 'filterActivePlugins'));
        add_filter('pre_update_site_option_active_sitewide_plugins', array($this, 'filterActiveSitewidePlugins'));
    }

    /**
     * This filter makes sure we load Treehouse CORE before any other Treehouse 
     * plugin or theme.
     * 
     * @param array $plugins an array with all plugins we can filter on
     * @access private
     */
    public function filterActivePlugins($plugins)
    {
        if (empty($plugins)) {
            return $plugins;
        }

        // Get the basename
        $f = preg_quote(basename(plugin_basename($this->pluginFile)));

        // Return an array with our plugin on top
        return array_merge(
            preg_grep('/' . $f . '$/', $plugins), preg_grep('/' . $f . '$/', $plugins, PREG_GREP_INVERT)
        );
    }

    /**
     * This filter makes sure we load Treehouse CORE before any other Treehouse 
     * plugin or theme when loaded as a sitewide plugin.
     * 
     * @param array $plugins an array with all plugins we can filter on
     * @access private
     */
    public function filterActiveSitewidePlugins($plugins)
    {

        if (empty($plugins)) {
            return $plugins;
        }

        $f = plugin_basename($this->pluginFile);

        if (isset($plugins[$f])) {
            // Remove the plugin
            unset($plugins[$f]);

            // And add it before all others
            return array_merge(array(
                $f => time(),
                ), $plugins);
        } 
        
        return $plugins;
    }
}
