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

        // Temporary test CPT
        CPT\Create::factory('products')
            ->setName(__('Products'))
            ->setNameSingular(__('Product'))
            ->setPublic(true)
            ->setHasArchive(true)
            ->setSupports(array('title', 'thumbnail'));
    }

}
