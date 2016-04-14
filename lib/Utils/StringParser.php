<?php

namespace LengthOfRope\Treehouse\Utils;

/**
 * This class provides some string utilities.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class StringParser
{

    protected $string = "";

    /**
     * Create the String object
     *
     * @param string $string The string to perform some things to
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * A simple factory to allow chaining
     *
     * @param string $string The string to perform some things to
     * @return \LengthOfRope\Treehouse\Utils\StringParser
     */
    public static function factory($string)
    {
        return new StringParser($string);
    }

    /**
     * This will trim each line in a multiline string.
     *
     * @return \LengthOfRope\Treehouse\Utils\StringParser
     */
    public function trimMultiline()
    {
        $this->string = trim(implode("\n", array_map('trim', explode("\n", $this->string))));

        return $this;
    }

    /**
     * Return the parsed string
     *
     * @return string
     */
    public function get()
    {
        return $this->string;
    }

    /**
     * Return the parsed string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

}
