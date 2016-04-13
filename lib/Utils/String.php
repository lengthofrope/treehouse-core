<?php

namespace LengthOfRope\Treehouse\Utils;

/**
 * This class provides some string utitlities.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class String
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

    public static function factory($string)
    {
        return new String($string);
    }

    /**
     * This will trim each line in a multiline string.
     *
     * @return string A multilined-trimmed string
     */
    public function trimMultiline()
    {
        return trim(implode("\n", array_map('trim', explode("\n", $this->string))));
    }

}
