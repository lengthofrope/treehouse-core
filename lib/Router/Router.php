<?php

namespace LengthOfRope\Treehouse\Router;

/**
 * Router simplifies the setup of template routes (or better said: changes in the template routes).
 * It will check if a slug exists in the theme (or child theme), and if not, it will create addiotional
 * checks if it exists in the plugin 'template' directory. If so it will use this one, otherwise
 * WordPress will fall back to the default single/ archive pages.
 *
 * If theme does no provide the following files:
 * - single-{slug}.php
 * - archive-(slug}.php
 *
 * This will check the plugin/ theme if the file exists in this location and serves that file
 * - /templates/single-{slug}.php
 * - /templates/archive-{slug}.php
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Router
{

    private $coreFile;

    /**
     * The constructor of a route.
     *
     * @param string $slug     The slug to create a route for.
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public function __construct($slug, $coreFile)
    {
        $this->slug     = $slug;
        $this->coreFile = $coreFile;
    }

    /**
     * A factory to construct the Router
     *
     * @param string $slug     The slug to create a route for.
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public static function factory($slug, $coreFile)
    {
        return new Router($slug, $coreFile);
    }

    /**
     * Setup a route for single pages.
     *
     * It will check if:
     * 1. the current active theme has a template file for the given slug;
     * 2. If not, it will check if the plugin requesting the route has a template file in the 'template'
     *    directory for the given slug;
     * 3. As a last resort falls back to the default theme's single.php (or index.php)
     *
     * @return \LengthOfRope\Treehouse\Router\Router
     */
    public function routeSingle()
    {
        add_filter('single_template', function($singleTemplate) {
            return $this->routeSingleTemplate($singleTemplate);
        });

        return $this;
    }

    /**
     * Handles the routing of the post type single template.
     *
     * @param string $singleTemplate The current selected single template
     * @return string The converted selected single template, if any
     */
    private function routeSingleTemplate($singleTemplate)
    {
        // Get the current post
        $post = get_post();

        if ($post->post_type !== $this->slug) {
            // Bail early
            return $singleTemplate;
        }

        // Check if this template is found in the theme or child theme
        $found = locate_template('single-' . $this->slug . '.php', false);

        if (empty($found)) {
            // The theme does not provide a template for the single, so provide our own
            $ourTemplate = dirname($this->coreFile) . '/templates/single-' . $this->slug . '.php';

            // If our plugin/ theme provides the file return it
            if (is_file($ourTemplate)) {
                $singleTemplate = $ourTemplate;
            }
        }

        return $singleTemplate;
    }

    /**
     * Setup a route for archive pages.
     *
     * It will check if:
     * 1. the current active theme has a template file for the given slug;
     * 2. If not, it will check if the plugin requesting the route has a template file in the 'template'
     *    directory for the given slug;
     * 3. As a last resort falls back to the default theme's archive.php (or index.php)
     *
     * @return \LengthOfRope\Treehouse\Router\Router
     */
    public function routeArchive()
    {
        add_filter('archive_template', function($archiveTemplate) {
            return $this->routeArchiveTemplate($archiveTemplate);
        });

        return $this;
    }

    /**
     * Handles the routing of the post type archive template.
     *
     * @param string $archiveTemplate The current selected archive template
     * @return string The converted selected archive template, if any
     */
    private function routeArchiveTemplate($archiveTemplate)
    {
        if (!is_post_type_archive($this->slug)) {
            // Bail early
            return $archiveTemplate;
        }

        // Check if this template is found in the theme or child theme
        $found = locate_template('archive-' . $this->slug . '.php', false);

        if (empty($found)) {
            // The theme does not provide a template for the single, so provide our own.
            $ourTemplate = dirname($this->coreFile) . '/templates/archive-' . $this->slug . '.php';

            // If our plugin/ theme provides the file return it
            if (is_file($ourTemplate)) {
                $archiveTemplate = $ourTemplate;
            }
        }

        return $archiveTemplate;
    }

    /**
     * Setup a route for taxonomy pages.
     *
     * It will check if:
     * 1. the current active theme has a template file for the given slug;
     * 2. If not, it will check if the plugin requesting the route has a template file in the 'template'
     *    directory for the given slug;
     * 3. As a last resort falls back to the default theme's taxonomy.php (or index.php)
     *
     * @return \LengthOfRope\Treehouse\Router\Router
     */
    public function routeTaxonomy()
    {
        add_filter('taxonomy_template', function($taxonomyTemplate) {
            return $this->routeTaxonomyTemplate($taxonomyTemplate);
        });

        return $this;
    }

    /**
     * Handles the routing of the taxonomy template.
     *
     * @param string $taxonomyTemplate The current selected taxonomy template
     * @return string The converted selected taxonomy template, if any
     */
    private function routeTaxonomyTemplate($taxonomyTemplate)
    {
        // TODO: create taxonomy router
        return $taxonomyTemplate;
    }

}
