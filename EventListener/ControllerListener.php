<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use PhpInk\Nami\CoreBundle\Util\Globals;
use PhpInk\Nami\CoreBundle\Twig\TwigExtension;

class ControllerListener
{
    /**
     * @var string
     */
    protected $host;
    protected $applicationDir;
    protected $environment;
    protected $uploadDir;
    
    protected $em;

    /**
     * @param string $host
     * @param string $appDir
     * @param string $env
     * @param string $uploadDir
     */
    public function __construct($host, $appDir, $env, $uploadDir, $pluginPath, TwigExtension $extension)
    {
        $this->host = $host;
        $this->applicationDir = $appDir;
        $this->environment = $env;
        $this->uploadDir = $uploadDir;
        $this->pluginPath = $pluginPath;
        $this->extension = $extension;
    }

    /**
     * @var DocumentManager|EntityManager $em
     */
    public function setManager($em)
    {
        $this->em = $em;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $controller = $event->getController();
            $this->extension->setController($controller);
            if (is_array($controller)) {
                $controller = reset($controller);
            }
            if ($controller) {
                Globals::setHost($this->host);
                Globals::setApplicationDir($this->applicationDir);
                Globals::setEnv($this->environment);
                Globals::setUploadDir($this->uploadDir);
                Globals::setPluginPath($this->pluginPath);
            }
        }
        return $event;
    }
}
