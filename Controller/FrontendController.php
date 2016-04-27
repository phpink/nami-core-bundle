<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpInk\Nami\CoreBundle\Util\Analytics;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\PageInterface;
use PhpInk\Nami\CoreBundle\Repository\CategoryRepositoryInterface;
use PhpInk\Nami\CoreBundle\Repository\PageRepositoryInterface;
use PhpInk\Nami\CoreBundle\Plugin\Registry as PluginRegistry;

class FrontendController extends Controller
{
    /**
     * @var \PhpInk\Nami\CoreBundle\Repository\Core\PageRepositoryInterface
     */
    protected $pageRepo;

    /**
     * @var \PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface
     */
    protected $categoryRepo;

    /**
     * Set page, category repositories
     */
    protected function initRepositories()
    {
        $em = $this->getManager();
        $this->pageRepo = $em->getRepository('NamiCoreBundle:Page');
        $this->categoryRepo = $em->getRepository('NamiCoreBundle:Category');
    }

    /**
     * Get Doctrine manager ORM or ODM
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getManager()
    {
        return ($this->container->getParameter('nami_core.database_adapter') === 'odm') ?
            $this->get('doctrine_mongodb')->getManager() :
            $this->getDoctrine()->getManager();
    }

    /**
     * @param Request $request Request
     * @param string  $slug    Page slug
     * @return Response
     */
    public function indexAction(Request $request, $slug = 'index')
    {
        $this->initRepositories();
        $slug = str_replace('.html', '', $slug);
        // Retrieve page or category from slug
        $page = $this->searchPage($slug);
        $response = null;
        if (!$page) {
            $response = new Response();
            $response->setStatusCode(404);
            $page = $this->get404Page();
        }
        // Plugins processing
        if ($page) {
            foreach ($page->getBlocks() as $block) {
                if ($block->getType() !== 'default') {
                    $this->processPlugin($request, $block);
                }
            }
        }
        return $this->render(
            'NamiCoreBundle:default:layout.html.twig',
            array(
                'menu' => $this->categoryRepo->getMenu(),
                'page' => $page
            ),
            $response
        );
    }

    /**
     * @param Request        $request
     * @param BlockInterface $block
     */
    protected function processPlugin(Request $request, $block)
    {
        $pluginRegistry = PluginRegistry::getInstance(
            $this->getParameter('nami_core.plugin_path')
        );
        if (!$pluginRegistry->getPlugins()) {
            $pluginRegistry->scanPlugins();
        }
        $pluginDetails = $pluginRegistry->getPlugin(
            $block->getType()
        );
        if (is_array($pluginDetails)
            &&  array_key_exists('block', $pluginDetails)
            &&  class_exists($pluginDetails['block'])) {
            $plugin = new $pluginDetails['block'](
                $this, $request, $block
            );
            $plugin->process(
                $this->container
            );
        }
    }

    /**
     * @param $slug string Page slug
     * @return PageInterface
     */
    protected function searchPage($slug)
    {
        $page = $this->pageRepo->getPageFromSlug($slug);
        if (!$page) {
            $category = $this->categoryRepo->getCategoryFromSlug($slug);
            if ($category) {
                $page = $category->generatePage();
            }
        } else {
            // Register analytics
            Analytics::registerPageHit(
                $this->getManager(),
                $this->getRequest()->getClientIp(),
                $this->getRequest()->headers->get('user-agent'),
                $page
            );
        }
        return $page;
    }


    /**
     * @return PageInterface
     */
    protected function get404Page()
    {
        $page = $this->pageRepo->createModel();
        $page->setTitle('Page non trouvée');
        $blockClass = '\PhpInk\Nami\CoreBundle\Model\\'.
            ($this->container->getParameter('nami_core.database_adapter') === 'odm' ?
                'Odm' : 'Orm') . '\Block';
        $block = new $blockClass(
            'Page introuvable',
            "<p class='mh'>La page que vous avez demandée n’a pas été trouvée.<br>
                    Il se peut que le lien que vous avez utilisé soit rompu ou que vous ayez tapé l’adresse (URL) incorrectement.</p>"
        );
        $page->addBlock($block);
        return $page;
    }
}
