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
    }

}
