<?php

namespace LengthOfRope\Treehouse\Template;

/**
 * This is an abstract Template which is used as the base for all template files.
 * It handles the reading and parsing of the xml files through PHPTAL.
 *
 * @author LengthOfRope, Bas de Kort <bdekort@gmail.com>
 */
abstract class Template extends \PHPTAL
{

    /**
     * PHPTAL Translator
     * @var \PHPTAL_GetTextTranslator $TalTranslator
     */
    private $TalTranslator;
    private $templateFile;

    /**
     * Constructor
     * @param string $templateFile The XML file to use as template
     * @param string $translationDomain The domain to use for translations
     * @throws \Exception
     */
    public function __construct($templateFile, $translationDomain)
    {
        $this->templateFile = $templateFile;

        if (!is_readable($this->templateFile)) {
            throw new \Exception(sprintf(__('File %s does not exist.', 'treehouse-core'), $this->templateFile), 404);
        }

        // Construct PHPTAL object
        parent::__construct();

        // Add translator
        $this->TalTranslator = new Services\Translation();
        $this->TalTranslator->useDomain($translationDomain);
        $this->setTranslator($this->TalTranslator);

        // Render as HTML5
        $this->setOutputMode(\PHPTAL::HTML5);

        // Strip comments
        $this->stripComments(true);

        // Set source
        $this->setTemplate($this->templateFile);
    }

    /**
     * Set TAL data to the template
     *
     * @param string|array $talKey Key value or array of key-value pairs
     * @param string|NULL $talValue The value of the key, if key is not an array
     *
     * @return \NextBuzz\SEO\PHPTAL\Template to allow chaining
     */
    public function setTalData($talKey, $talValue = null)
    {
        if (is_array($talKey)) {
            foreach ($talKey as $key => $value) {
                $this->setTalData($key, $value);
            }

            return $this;
        }

        $this->set($talKey, $talValue);

        return $this;
    }

}
