<?php

namespace LengthOfRope\Treehouse\CPT;

/**
 * A helper class to create Custom Post Types.
 *
 * It handles:
 * - Creation of the post type (with a prefix by default)
 * - Adding templates within the plugin for singles and archives.
 * - Allows basic settings to be set through chaining
 *
 * Usage (ordinary mode):
 * <code>
 * new \LengthOfRope\Treehouse\CPT\Create('products', array(
 *      'labels'      => array(
 *          'name'          => __('Products'),
 *          'singular_name' => __('Product')
 *      ),
 *      'public'      => true,
 *      'has_archive' => true,
 *  ));
 * </code>
 *
 * Usage (chained mode):
 * <code>
 * \LengthOfRope\Treehouse\CPT\Create::factory('products')
 *      ->setName(__('Products'))
 *      ->setNameSingular(__('Product')))
 *      ->setPublic(true)
 *      ->setHasArchive(true);
 * </code>
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Create
{

    private $created = false;
    protected $slug;
    protected $args;

    /**
     * Setup a new posttype
     *
     * @param string $slug The unique slug for this post type.
     * @param array $args
     * @param string $slugPrefix
     */
    public function __construct($slug, $args = array(), $slugPrefix = 'th_')
    {
        // Add the slugPrefix to make sure it is a unique posttype.
        $this->slug = $slugPrefix . $slug;

        // Make sure the 'slug' on the frontend does not have the prefix
        if (is_array($args) && $slugPrefix !== '' && !isset($args['rewrite'])) {
            $args['rewrite'] = array(
                'slug' => $slug
            );
        }

        $this->args = $args;

        add_action('init', array($this, 'registerCPT'));
    }

    /**
     * Register the actual post type
     *
     * @access private
     */
    public function registerCPT()
    {
        $this->created = true;
        register_post_type($this->slug, $this->args);
    }

    /**
     * Factory to allow chaining
     *
     * @param string $slug The unique slug for this post type.
     * @param array $args
     * @param string $slugPrefix
     */
    public static function factory($slug, $args = array(), $slugPrefix = 'th_')
    {
        return new Create($slug, $args, $slugPrefix);
    }

    /**
     * Set CPT label name
     *
     * @param string $name
     * @return \LengthOfRope\Treehouse\CPT\Create
     * @throws \Exception
     */
    public function setName($name)
    {
        if ($this->created) {
            throw new \Exception(__("Doing it wrong! Creation of CPT has already happened!", "treehouse-core"), 500);
        }

        if (!isset($this->args['labels'])) {
            $this->args['labels'] = array();
        }

        $this->args['labels']['name'] = $name;

        return $this;
    }

}
