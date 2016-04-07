<?php

namespace LengthOfRope\Treehouse;

/**
 * The base class all themes should extend to.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
abstract class ThemeComponent extends Component
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
        add_action('after_setup_theme', array($this, 'loadTextDomain'));
    }

    /**
     * Load the theme (and child-theme) text-domain
     *
     * @access private
     */
    public function loadTextDomain()
    {
        $themeBase = basename(dirname($this->coreFile));
        load_theme_textdomain($themeBase, get_template_directory() . '/languages');
        load_theme_textdomain($themeBase, get_stylesheet_directory() . '/languages');
    }

}
