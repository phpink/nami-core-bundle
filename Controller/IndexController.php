<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Event\PluginRegisterEvent;
use PhpInk\Nami\CoreBundle\Plugin\Registry as PluginRegistry;
use PhpInk\Nami\CoreBundle\Util\Globals;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Index Rest controller (Ping, Login)
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class IndexController extends AbstractController
{
    /**
     * Get main data
     *
     * @Annotations\Get("/")
     * @ApiDoc(
     *   description = "Get the store details.",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when values are successful returned"
     *   }
     * )
     *
     * @return View
     */
    public function indexAction()
    {
        $pageRepo = $this->getRepository('Page');
        $appConfig = $this->getRepository('Configuration')->getValues(
            array('title', 'slogan')
        );
        $appSize = $this->getDirectorySize(
            $this->container->getParameter('kernel.root_dir')
        );
        $data = array(
            'app' =>  $appConfig,
            'users' => $this->getRepository('User')->countItems(),
            'pages' => $pageRepo->countItems(),
            'lastUpdate' => $pageRepo->getLastUpdate(),
            'appSize' => $appSize
        );
        return View::create($data);
    }

    /**
     * Get config data
     *
     * @Annotations\Get("/configuration")
     * @ApiDoc(
     *   description = "Get the configuration parameters.",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when config successful returned"
     *   }
     * )
     *
     * @return View
     */
    public function getConfigurationAction()
    {
        $data = array();
        $configParams = $this->getRepository('Configuration')->findAll();
        foreach ($configParams as $configParam) {
            $key = $configParam->getName();
            $data[$key] = $configParam->getValue();
        }
        return View::create($data);
    }

    /**
     * Update config
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return View
     * @throws \HttpInvalidParamException
     *
     * @Annotations\Put("/configuration")
     * @ApiDoc(
     *   description = "Update or create a store configuration parameter.",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when configuration update successful",
     *     401 = "Returned when not logged"
     *   },
     *  parameters={
     *      {
     *          "name"="name", "dataType"="string",
     *          "required"=true, "description"="The parameter key."
     *      },
     *      {
     *          "name"="value", "dataType"="string",
     *          "required"=true, "description"="The parameter value."
     *      }
     *  }
     * )
     * @Annotations\RequestParam(
     *    name="name", requirements="[a-zA-Z-_]+",
     *    description="Parameter key."
     * )
     * @Annotations\RequestParam(
     *     name="value", description="Parameter value."
     * )
     */
    public function putConfigurationAction(ParamFetcherInterface $paramFetcher)
    {
        $this->checkIsAdmin();
        $name  = $this->getRequiredRequestParam($paramFetcher, 'name');
        $value = $this->getRequiredRequestParam($paramFetcher, 'value');
        $configuration = null;
        $code = Codes::HTTP_BAD_REQUEST;
        if ($name && $value) {
            $code = Codes::HTTP_OK;
            $repo = $this->getRepository('Configuration');
            $configuration = $repo->findOneByName($name);
            if (!$configuration) {
                $code = Codes::HTTP_CREATED;
                $configuration = $repo->createModel($name);
            }
            $configuration->setValue($value);
            $this->saveModel($configuration, $code);
        }
        return View::create($configuration, $code);
    }

    /**
     * Read plugins from the directory
     *
     * @Annotations\Get("/plugins")
     * @ApiDoc(
     *   description = "Get the plugins.",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when plugins successful returned"
     *   }
     * )
     *
     * @return View
     */
    public function getPluginsAction()
    {
        $this->checkIsAdmin();
        $registeredPlugins = [];
        $event = new PluginRegisterEvent($registeredPlugins);
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(PluginRegisterEvent::NAME, $event);
        
        return View::create($registeredPlugins);
    }

    /**
     * Get the size of a directory in megabytes
     *
     * @param string $directory The directory to scan.
     *
     * @return int
     */
    protected function getDirectorySize($directory)
    {
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        foreach ($iterator as $file) {
            $size += $file->getSize();
        }
        return round($size / 1024 / 1024, 2);
    }

    /**
     * Read plugins from the directory
     *
     * @Annotations\Delete("/cache")
     * @ApiDoc(
     *   description = "Clear the application cache.",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when cache has been cleared"
     *   }
     * )
     *
     * @return View
     */
    public function cacheClearAction()
    {
        $this->checkIsAdmin();
        $kernel = $this->get('kernel');
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);
        $options = array(
            'command' => 'cache:clear',
            "--env" => Globals::getEnv(),
            '--no-warmup' => true
        );
        return new JsonResponse([
            'cache' =>
                $application->run(new \Symfony\Component\Console\Input\ArrayInput($options))
        ]);
    }
}
