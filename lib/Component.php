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
        if (defined('TREEHOUSE_DEV_MODE') && TREEHOUSE_DEV_MODE === true) {
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

    }

}
