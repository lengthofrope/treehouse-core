<?php

namespace LengthOfRope\Treehouse;

/**
 * The core class makes sure everything is setup properly for all other
 * plugins and themes before they are loaded.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Core extends PluginComponent
{

    public function __construct()
    {
        // Make sure Core is loaded before any other Treehouse plugin
        new Utils\PluginLoadPriority(TH_CORE_FILE);

        parent::__construct(TH_CORE_FILE);

        // Test CPT
        new CPT\Create('treehouse',
            array('labels'      => array(
                'name'          => __('Products'),
                'singular_name' => __('Product')
            ),
            'public'      => true,
            'has_archive' => true,
        ));

        /* CPT\Create::factory('products2')
          ->setName(__('Products2'))
          ->setNameSingular(__('Product2'))
          ->setPublic(true)
          ->setHasArchive(true); */
    }

}
