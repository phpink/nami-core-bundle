<?php

namespace PhpInk\Nami\CoreBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use PhpInk\Nami\CoreBundle\Repository\LocaleRepository;

/**
 * Store Globals from config
 *
 * @package PhpInk\Nami\CoreBundle\Util
 */
class Globals
{
    /**
     * Application directory
     * (%kernel.root_dir%)
     * @var string
     */
    protected static $applicationDir;

    /**
     * Application host
     * (%host%)
     * @var string
     */
    protected static $host;

    /**
     * Environment (dev, prod)
     * @var string
     */
    protected static $env;

    /**
     * Upload directory
     * (%nami_core.upload_dir%)
     * @var string
     */
    protected static $uploadDir;

    /**
     * Plugin directory
     * (%nami_core.plugin_path%)
     * @var string
     */
    protected static $pluginPath;

    /**
     * Get the value of applicationDir.
     *
     * @return string
     */
    public static function getApplicationDir()
    {
        return self::$applicationDir;
    }

    /**
     * Set the value of applicationDir.
     *
     * @param string $applicationDir
     */
    public static function setApplicationDir($applicationDir)
    {
        self::$applicationDir = $applicationDir;
    }

    /**
     * Get the value of host.
     *
     * @return string
     */
    public static function getHost()
    {
        return self::$host;
    }

    /**
     * Set the value of host.
     *
     * @param string $host
     */
    public static function setHost($host)
    {
        self::$host = $host;
    }

    /**
     * Get the value of env.
     *
     * @return string
     */
    public static function getEnv()
    {
        return self::$env;
    }

    /**
     * Set the value of env.
     *
     * @param string $env
     */
    public static function setEnv($env)
    {
        self::$env = $env;
    }

    /**
     * Get the value of uploadDir.
     *
     * @param boolean $absolute Get the absolute path
     * @return string
     */
    public static function getUploadDir($absolute = true)
    {
        $uploadDir = self::$uploadDir;
        if ($absolute) {
            $uploadDir = dirname(self::getApplicationDir()) .
                DIRECTORY_SEPARATOR . 'web' .
                $uploadDir;
        }
        return $uploadDir;
    }

    /**
     * Set the value of uploadDir.
     *
     * @param string $uploadDir
     */
    public static function setUploadDir($uploadDir)
    {
        self::$uploadDir = $uploadDir;
    }

    /**
     * Get the value of pluginPath
     * @return string
     */
    public static function getPluginPath()
    {
        return self::$pluginPath;
    }

    /**
     * Set the value of pluginPath
     * @param string $pluginPath
     */
    public static function setPluginPath($pluginPath)
    {
        self::$pluginPath = $pluginPath;
    }
}
