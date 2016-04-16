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

        // Set some default values so we don't have to check for them in all set methods
        $defaults = array(
            'labels' => array(),
            'supports' => array(),
            'taxonomies' => array(),
        );
        $this->args = array_merge($defaults, $args);

        add_action('init', function() {
            $this->registerCPT();
        });
    }

    /**
     * Callback tgat registers the actual post type
     *
     */
    private function registerCPT()
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
     * @throws \Exception If called to late
     */
    public function setName($name)
    {
        $this->checkCreated();

        $this->args['labels']['name'] = $name;

        return $this;
    }

    /**
     * Set CPT label singular_name
     *
     * @param string $name
     * @return \LengthOfRope\Treehouse\CPT\Create
     * @throws \Exception If called to late
     */
    public function setNameSingular($name)
    {
        $this->checkCreated();

        $this->args['labels']['singular_name'] = $name;

        return $this;
    }

    /**
     * Set CPT as public
     *
     * @param boolean $boolean
     * @return \LengthOfRope\Treehouse\CPT\Create
     * @throws \Exception If called to late
     */
    public function setPublic($boolean)
    {
        $this->checkCreated();

        $this->args['public'] = $boolean;

        return $this;
    }

    /**
     * Set CPT as archive
     *
     * @param boolean $boolean
     * @return \LengthOfRope\Treehouse\CPT\Create
     * @throws \Exception If called to late
     */
    public function setHasArchive($boolean)
    {
        $this->checkCreated();

        $this->args['has_archive'] = $boolean;

        return $this;
    }

    /**
     * Set CPT supports:
     * - title
     * - editor
     * - author
     * - thumbnail
     * - excerpt
     * - trackbacks
     * - custom-fields
     * - comments
     * - revisions
     * - page-attributes
     * - post-formats
     *
     * @param array $supports Array of supported items
     * @return \LengthOfRope\Treehouse\CPT\Create
     * @throws \Exception If called to late
     */
    public function setSupports($supports)
    {
        $this->checkCreated();

        $this->args['supports'] = $supports;

        return $this;
    }

    /**
     * Throw an Exception if called to late in WordPress init process.
     *
     * @throws \Exception
     */
    private function checkCreated()
    {
        if ($this->created) {
            throw new \Exception(__("Doing it wrong! Creation of CPT has already happened!", "treehouse-core"), 500);
        }
    }

}
