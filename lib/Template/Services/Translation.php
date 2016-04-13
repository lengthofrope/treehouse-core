<?php

namespace LengthOfRope\Treehouse\Template\Services;

/**
 * A simple service which maps the WordPress translation functionality to PHPTAL
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
class Translation implements \PHPTAL_TranslationService
{

    private $domain   = 'default';
    private $encoding = 'UTF-8';

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function setLanguage()
    {
        throw new \Exception("Use WordPress Translate Core to set Language!", 500);
    }

    public function setVar($key, $value)
    {
        throw new \Exception(sprint_f("Use WordPress Translate Core to set var '%s' with value '%s'!", $key, $value),
        500);
    }

    public function translate($key, $htmlescape = true)
    {
        if ($htmlescape) {
            return esc_html(__(String::factory($key)->trimMultiline(), $this->domain));
        }

        return __(String::factory($key)->trimMultiline(), $this->domain);
    }

    public function useDomain($domain)
    {
        $this->domain = $domain;
    }

}
