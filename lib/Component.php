<?php

namespace LengthOfRope\Treehouse;

/**
 * The base class all plugin and themes should extend to.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
abstract class Component
{

    protected $coreFile;

    /**
     * Constructor which loads plugin core.
     *
     * @param string $coreFile The __FILE__ variable of the plugin/ theme location
     */
    public function __construct($coreFile)
    {
        $this->coreFile = $coreFile;

        // Do some additional stuff when in dev mode
        $this->initDevMode();
    }

    /**
     * Handle some stuff that should not be run if not in plugin development mode.
     */
    private function initDevMode()
    {
        if (!defined('TREEHOUSE_DEV_MODE') || TREEHOUSE_DEV_MODE === false) {
            // Bail early
            return;
        }

        $this->updatePOT();
    }

    /**
     * Update the POT file with all translations.
     */
    private function updatePOT()
    {
        $dir     = dirname($this->coreFile);
        $base    = basename($dir);
        $potFile = $dir . '/languages/' . $base . '.pot';

        // If POT file exists, bail
        if (file_exists($potFile)) {
            return;
        }

        if (!is_dir(dirname($potFile))) {
            mkdir(dirname($potFile), 0755, true);
        }

        // Create the POT file
        $createPot = new Utils\GeneratePOT();
        $createPot
            ->addPath($dir . '/tpl/', "xml")
            ->addPath($dir . '/lib/', "php")
            ->stripPathInComments($dir)
            ->parse()
            ->writePot(ucwords(str_replace(array('-', '_'), ' ', $base)), $potFile);
    }

}
