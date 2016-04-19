<?php

namespace LengthOfRope\Treehouse\Register;

/**
 * A helper class to create Custom Taxonomies.
 *
 * It handles:
 * - Creation of the taxonomy (with a prefix by default)
 * - Adding templates within the plugin for singles and archives (using setupTemplates method).
 * - Allows basic settings to be set through chaining
 *
 * Note: To make sure all translations are ready this should be called in an init action!
 *
 * Usage (ordinary mode):
 * <code>
 * new \LengthOfRope\Treehouse\Register\Taxonomy('product_categories', array(
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
 * \LengthOfRope\Treehouse\Register\Taxonomy::factory('product_categories')
 *      ->setLabels(__('Product categories'), __('Product category'))
 *      ->setPublic(true)
 *      ->setHasArchive(true);
 * </code>
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Taxonomy
{

    private $coreFile;
    protected $slug;
    protected $objectType;
    protected $args;

    /**
     * Setup a new taxonomy
     *
     * @param string $slug The unique slug for this taxonomy.
     * @param string|array $objectType Name of the object type for the taxonomy object.
     *                                 Object-types can be built-in Post Type or any Custom Post Type
     *                                 that may be registered. If null, the taxonomy must be manually added to the
     *                                 custom post type where it should be available.
     *                                 Note: remember the slugPrefix the Treehouse classes are adding!
     * @param array $args
     * @param string $slugPrefix
     */
    public function __construct($slug, $objectType = null, $args = array(), $slugPrefix = 'th_tax_')
    {
        // Add the slugPrefix to make sure it is a unique posttype.
        $this->slug = $slugPrefix . $slug;

        $this->objectType = $objectType;

        // Make sure the 'slug' on the frontend does not have the prefix
        if (is_array($args) && $slugPrefix !== '' && !isset($args['rewrite'])) {
            $args['rewrite'] = array(
                'slug' => $slug
            );
        }

        // Set some default values so we don't have to check for them in all set methods
        $defaults   = array(
            'labels' => array(),
        );
        $this->args = array_merge($defaults, $args);

        // Register the post type late in the init process, since it is probably created in a init action as well.
        add_action('init', function() {
            $this->registerTaxonomy();
        }, 99);
    }

    /**
     * Callback tgat registers the actual taxonomy
     */
    private function registerTaxonomy()
    {
        register_taxonomy($this->slug, $this->objectType, $this->args);
    }

    /**
     * Factory to allow chaining
     *
     * @param string $slug The unique slug for this taxonomy.
     * @param string|array $objectType Name of the object type for the taxonomy object.
     *                                 Object-types can be built-in Post Type or any Custom Post Type
     *                                 that may be registered. If null, the taxonomy must be manually added to the
     *                                 custom post type where it should be available.
     * @param array $args
     * @param string $slugPrefix
     */
    public static function factory($slug, $objectType = null, $args = array(), $slugPrefix = 'th_')
    {
        return new Taxonomy($slug, $objectType, $args, $slugPrefix);
    }

    /**
     * A plural descriptive name for the taxonomy marked for translation.
     *
     * @param string $label Descriptive name (plural)
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setLabel($label)
    {
        $this->args['label'] = $label;

        return $this;
    }

    /**
     * An array of labels for this taxonomy.
     * By default tag labels are used for non-hierarchical types and category labels for hierarchical ones.
     * Default: if empty, name is set to label value, and singular_name is set to name value
     *
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @param string|array $labels If (plural) string, and singularName is also used, auto-generates all labels
     * @param string|false $singularName If string and labels is plural string, auto-generates all labels
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setLabels($labels, $singularName = false)
    {
        if (is_array($labels)) {
            $this->args['labels'] = $labels;
            return $this;
        }

        if (is_string($singularName) && is_string($labels)) {
            $pluralName           = $labels;
            $pluralLC             = \LengthOfRope\Treehouse\Utils\StringParser::factory($pluralName)->toLower();
            $this->args['labels'] = array(
                'name'                       => $pluralName,
                'singular_name'              => $singularName,
                'menu_name'                  => $pluralName,
                'all_items'                  => sprintf(__('All %s', 'treehouse-core'), $pluralName),
                'edit_item'                  => sprintf(__('Edit %s', 'treehouse-core'), $singularName),
                'view_item'                  => sprintf(__('View %s', 'treehouse-core'), $singularName),
                'update_item'                => sprintf(__('Update %s', 'treehouse-core'), $singularName),
                'add_new_item'               => sprintf(__('Add New %s', 'treehouse-core'), $singularName),
                'new_item_name'              => sprintf(__('New %s Name', 'treehouse-core'), $singularName),
                'parent_item'                => sprintf(__('Parent %s', 'treehouse-core'), $singularName),
                'parent_item_colon'          => sprintf(__('Parent %s:', 'treehouse-core'), $singularName),
                'search_items'               => sprintf(__('Search %s', 'treehouse-core'), $pluralName),
                'popular_items'              => sprintf(__('Popular %s', 'treehouse-core'), $pluralName),
                'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'treehouse-core'), $pluralLC),
                'add_or_remove_items'        => sprintf(__('Add or remove %s', 'treehouse-core'), $pluralLC),
                'choose_from_most_used'      => sprintf(__('Choose from most used %s', 'treehouse-core'), $pluralLC),
                'not_found'                  => sprintf(__('No %s found', 'treehouse-core'), $pluralLC),
            );
        }

        return $this;
    }

    /**
     * Include a description of the taxonomy.
     *
     * @param string $description Include a description of the taxonomy.
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setDescription($description)
    {
        $this->args['description'] = $description;

        return $this;
    }

    /**
     * If the taxonomy should be publicly queryable.
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setPublic($boolean)
    {
        $this->args['public'] = $boolean;

        return $this;
    }

    /**
     * Whether to generate a default UI for managing this taxonomy.
     * Default: if not set, defaults to value of public argument. As of 3.5, setting this to false for
     * attachment taxonomies will hide the UI.
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowUI($boolean)
    {
        $this->args['show_ui'] = $boolean;

        return $this;
    }

    /**
     * True makes this taxonomy available for selection in navigation menus.
     * Default: if not set, defaults to value of public argument
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowInNavMenus($boolean)
    {
        $this->args['show_in_nav_menus'] = $boolean;

        return $this;
    }

    /**
     * Where to show the taxonomy in the admin menu. show_ui must be true.
     * Default: value of show_ui argument
     *     'false' - do not display in the admin menu
     *     'true' - show as a submenu of associated object types
     *
     * @param boolean $value True, or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowInMenu($value)
    {
        $this->args['show_in_menu'] = $value;

        return $this;
    }

    /**
     * Whether to allow the Tag Cloud widget to use this taxonomy.
     * Default: if not set, defaults to value of show_ui argument
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowTagcloud($boolean)
    {
        $this->args['show_tagcloud'] = $boolean;

        return $this;
    }

    /**
     * Whether to show the taxonomy in the quick/bulk edit panel. (Available since 4.2)
     * Default: if not set, defaults to value of show_ui argument
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowInQuickEdit($boolean)
    {
        $this->args['show_in_quick_edit'] = $boolean;

        return $this;
    }

    /**
     * Provide a callback function name for the meta box display. (Available since 3.8)
     * Default: null
     *
     * Note: Defaults to the categories meta box (post_categories_meta_box() in meta-boxes.php) for hierarchical
     * taxonomies and the tags meta box (post_tags_meta_box()) for non-hierarchical taxonomies. No meta box
     * is shown if set to false.
     *
     * @param function|false $callback
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setMetaBoxCB($callback)
    {
        $this->args['meta_box_cb'] = $callback;

        return $this;
    }

    /**
     * Whether to allow automatic creation of taxonomy columns on associated post-types table. (Available since 3.5)
     * Default: false
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setShowAdminColumn($boolean)
    {
        $this->args['show_admin_column'] = $boolean;

        return $this;
    }

    /**
     * Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.
     * Default: false
     *
     * Note: Hierarchical taxonomies will have a list with checkboxes to select an existing category in the
     * taxonomy admin box on the post edit page (like default post categories). Non-hierarchical taxonomies will
     * just have an empty text field to type-in taxonomy terms to associate with the post (like default post tags).
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setHierarchical($boolean)
    {
        $this->args['hierarchical'] = $boolean;

        return $this;
    }

    /**
     * A function name that will be called when the count of an associated $object_type, such as post, is updated.
     * Works much like a hook.
     * Default: None - but see Note, below.
     *
     * Note: While the default is '', when actually performing the count update in wp_update_term_count_now(), if
     * the taxonomy is only attached to post types (as opposed to other WordPress objects, like user),
     * the built-in _update_post_term_count() function will be used to count only published posts associated
     * with that term, otherwise _update_generic_term_count() will be used instead, that does no such checking.
     *
     * This is significant in the case of attachments. Because an attachment is a type of post, the
     * default _update_post_term_count() will be used. However, this may be undesirable, because this will
     * only count attachments that are actually attached to another post (like when you insert an image into
     * a post). This means that attachments that you simply upload to WordPress using the Media Library, but
     * do not actually attach to another post will not be counted. If your intention behind associating a
     * taxonomy with attachments was to leverage the Media Library as a sort of Document Management solution,
     * you are probably more interested in the counts of unattached Media items, than in those attached to posts.
     * In this case, you should force the use of _update_generic_term_count() by setting '_update_generic_term_count'
     * as the value for update_count_callback.
     *
     * Another important consideration is that _update_post_term_count() only counts published posts. If you are
     * using custom statuses, or using custom post types where being published is not necessarily a consideration
     * for being counted in the term count, then you will need to provide your own callback that doesn't include
     * the post_status portion of the where clause.
     *
     * @param string $functionName A function name that will be called when the count is updated.
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setUpdateCountCB($functionName)
    {
        $this->args['update_count_callback'] = $functionName;

        return $this;
    }

    /**
     * False to disable the query_var, set as string to use custom query_var instead of default which
     * is $taxonomy, the taxonomy's "name".
     * Default: $taxonomy
     *
     * Note: The query_var is used for direct queries through WP_Query like
     * new WP_Query(array('people'=>$person_name)) and URL queries like /?people=$person_name. Setting query_var
     * to false will disable these methods, but you can still fetch posts with an explicit WP_Query taxonomy
     * query like WP_Query(array('taxonomy'=>'people', 'term'=>$person_name)).
     *
     * @param boolean|string $value True or false or string
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setQueryVar($value)
    {
        $this->args['query_var'] = $value;

        return $this;
    }

    /**
     * Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to
     * override default URL settings for permalinks as outlined below:
     * Default: true
     *     'slug'         - Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug)
     *     'with_front'   - allowing permalinks to be prepended with front base - defaults to true
     *     'hierarchical' - true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false
     *     'ep_mask'      - (Required for pretty permalinks) Assign an endpoint mask for this taxonomy -
     *                      defaults to EP_NONE. If you do not specify the EP_MASK, pretty permalinks will not work.
     *                      For more info see this Make WordPress Plugins summary of endpoints.
     *
     * Note: You may need to flush the rewrite rules after changing this. You can do it manually by going to the
     * Permalink Settings page and re-saving the rules -- you don't need to change them -- or by calling
     * $wp_rewrite->flush_rules(). You should only flush the rules once after the taxonomy has been created, not
     * every time the plugin/theme loads.
     *
     * @param boolean|array $rewrite array with rewrite stuff
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setRewrite($rewrite)
    {
        $this->args['rewrite'] = $rewrite;

        return $this;
    }

    /**
     * An array of the capabilities for this taxonomy.
     * Default: None
     *     'manage_terms' - 'manage_categories'
     *     'edit_terms'   - 'manage_categories'
     *     'delete_terms' - 'manage_categories'
     *     'assign_terms' - 'edit_posts'
     *
     * @param array $capabilities array with capabilities
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setCapabilities($capabilities)
    {
        $this->args['capabilities'] = $capabilities;

        return $this;
    }

    /**
     * Whether this taxonomy should remember the order in which terms are added to objects.
     * Default: None
     *
     * @param boolean $boolean True or false
     * @return \LengthOfRope\Treehouse\Register\Taxonomy
     */
    public function setSort($boolean)
    {
        $this->args['sort'] = $boolean;

        return $this;
    }

    /**
     * Setup template routes for this taxonomy.
     *
     * If theme does no provide the following files:
     * - single-{slug}.php
     * - archive-(slug}.php
     *
     * This will check the plugin/ theme if the file exists in this location and serves that file
     * - /templates/single-{slug}.php
     * - /templates/archive-{slug}.php
     *
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public function setupRoutes($coreFile)
    {
        $this->coreFile = $coreFile;

        \LengthOfRope\Treehouse\Router\Router::factory($this->slug, $coreFile)
            ->routeTaxonomy();
    }

}
