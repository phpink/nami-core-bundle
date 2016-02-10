<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Core;

use PhpInk\Nami\CoreBundle\Model\Locale;

/**
 * Locale Doctrine relation
 * for an **_Locale Document
 */
trait LocalizedTrait
{
    /**
     * @var array
     * @ODM\Hash
     * @JMS\Expose
     * @JMS\Type("array<string, string>")
     */
    protected $locales = array();

    /**
     * Add a locale
     *
     * @param string $localeCode
     * @param string $localeString
     * @return self
     */
    public function addLocale($localeCode, $localeString)
    {
        $localeCode = strtolower($localeCode);
        $this->locales[$localeCode] = $localeString;

        return $this;
    }

    /**
     * Remove a locale
     *
     * @param string $localeCode
     * @return self
     */
    public function removeLocale($localeCode)
    {
        if (array_key_exists($localeCode, $this->locales)) {
            unset($this->locales[$localeCode]);
        }

        return $this;
    }

    /**
     * Get locales entity collection (one to many).
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Get locale entity from collection.
     *
     * @param string $code
     * @return mixed
     */
    public function getLocale($code)
    {
        $localeReturned = null;
        foreach ($this->locales as $currentCode => $locale) {
            if ($currentCode === $code) {
                $localeReturned = $locale;
            }
        }
        return $localeReturned;
    }
}
