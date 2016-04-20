<?php

namespace LengthOfRope\Treehouse\Register;

/**
 * A helper class to create Sidebars.
 *
 * It handles:
 * - Creation of the sidebar (with a prefix by default)
 * - Allows basic settings to be set through chaining
 *
 * Note: To make sure all translations are ready this should be called in an init action!
 *
 * Usage (ordinary mode):
 * <code>
 * new \LengthOfRope\Treehouse\Register\Sidebar('id', array(
 *      'name'        => __('Sidebar X'),
 *      'description' => '',
 *      'class'       => '',
 *  ));
 * </code>
 *
 * Usage (chained mode):
 * <code>
 * \LengthOfRope\Treehouse\Register\Sidebar::factory('id')
 *      ->setName(__('Sidebar X'))
 *      ->setDescription('')
 *      ->setClass('');
 * </code>
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Sidebar
{

    protected $identifier;
    protected $args;

    /**
     * Setup a new sidebar
     *
     * @param string $identifier The unique slug for this sidebar.
     * @param array $args
     */
    public function __construct($identifier, $args = array())
    {
        // Add the slugPrefix to make sure it is a unique posttype.
        $this->identifier = $identifier;

        $this->args = $args;

        // Register the sidebar late in the init process, since it is probably created in a init action as well.
        add_action('init', function() {
            $this->registerSidebar();
        }, 99);
    }

    /**
     * Callback tgat registers the actual sidebar
     */
    private function registerSidebar()
    {
        // Override id with given id
        $this->args['id'] = $this->identifier;

        register_sidebar($this->args);
    }

    /**
     * Factory to allow chaining
     *
     * @param string $identifier The unique id for this sidebar.
     * @param array $args
     * @param string $slugPrefix
     */
    public static function factory($identifier, $args = array())
    {
        return new Sidebar($identifier, $args);
    }

    /**
     * Sidebar name (default is localized 'Sidebar' and numeric ID).
     *
     * @param string $name Set the name
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setName($name)
    {
        $this->args['name'] = $name;

        return $this;
    }

    /**
     * Text description of what/where the sidebar is. Shown on widget management screen.
     *
     * @param string $description Include a description of the sidebar.
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setDescription($description)
    {
        $this->args['description'] = $description;

        return $this;
    }

    /**
     * CSS class to assign to the Sidebar in the Appearance -> Widget admin page. This class will only appear
     * in the source of the WordPress Widget admin page. It will not be included in the frontend of your website.
     * Note: The value "sidebar" will be prepended to the class value. For example, a class of "tal" will result
     * in a class value of "sidebar-tal". (default: empty).
     *
     * @param string $class Include a class in the admin pages
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setClass($class)
    {
        $this->args['class'] = $class;

        return $this;
    }

    /**
     * HTML to place before every widget (default: <li id="%1$s" class="widget %2$s">)
     *
     * Note: uses sprintf for variable substitution
     *
     * @param string $beforeWidget HTML to place before a widget
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setBeforeWidget($beforeWidget)
    {
        $this->args['before_widget'] = $beforeWidget;

        return $this;
    }

    /**
     * HTML to place after every widget (default: </li>\n).
     *
     * @param string $afterWidget HTML to place after a widget
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setAfterWidget($afterWidget)
    {
        $this->args['after_widget'] = $afterWidget;

        return $this;
    }

    /**
     * HTML to place before every title (default: <h2 class="widgettitle">).
     *
     * @param string $beforeTitle HTML to place before a widget title
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setBeforeTitle($beforeTitle)
    {
        $this->args['before_title'] = $beforeTitle;

        return $this;
    }

    /**
     * HTML to place after every title (default: </h2>\n).
     *
     * @param string $afterTitle HTML to place after a widget title
     * @return \LengthOfRope\Treehouse\Register\Sidebar
     */
    public function setAfterTitle($afterTitle)
    {
        $this->args['after_title'] = $afterTitle;

        return $this;
    }

}
