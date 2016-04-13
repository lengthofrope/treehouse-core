<?php

namespace LengthOfRope\Treehouse\Utils;

/**
 * This class provides some string utitlities.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class String
{

    /**
     * This will trim each line in a multiline string.
     * 
     * @param string $string
     * @return string
     */
    public static function trimMultiline($string)
    {
        return trim(implode("\n", array_map('trim', explode("\n", $string))));
    }

}
