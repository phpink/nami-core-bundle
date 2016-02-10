<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Core;

use PhpInk\Nami\CoreBundle\Util\Globals;
use PhpInk\Nami\CoreBundle\Model\Orm\Locale;

/**
 * Locale Doctrine relation
 * for an **_Locale Document
 *
 * NOTE: As locale is Unique,
 * Entities using this trait must have another Unique field
 */
trait LocaleItemTrait
{
    /**
     * Set Locale entity (one to many).
     *
     * @param Locale $locale
     * @return self
     */
    public function setLocale(Locale $locale = null)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get Locale entity (one to many).
     *
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get the locale ID
     *
     * @return string|null
     */
    public function getLocaleId()
    {
        return $this->getLocale() ?
            $this->getLocale()->getId() : null;
    }

    /**
     * Get the locale code
     *
     * @return string|null
     */
    public function getLocaleCode()
    {
        $code = null;
        if ($this->getLocale()) {
            $code = $this->getLocale()->getCode();
        }
        return $code;
    }
}
