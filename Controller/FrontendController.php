<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface;
use PhpInk\Nami\CoreBundle\Repository\Core\MenuRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpInk\Nami\CoreBundle\Util\Analytics;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\PageInterface;
use PhpInk\Nami\CoreBundle\Repository\Core\PageRepositoryInterface;
use PhpInk\Nami\CoreBundle\Event\BlockRenderEvent;
use PhpInk\Nami\CoreBundle\Plugin\Registry as PluginRegistry;

class FrontendController extends Controller
{
    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepo;

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepo;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepo;

    /**
     * Set page, category repositories
     */
    protected function initRepositories()
    {
        $em = $this->getManager();
        $this->pageRepo = $em->getRepository('NamiCoreBundle:Page');
        $this->menuRepo = $em->getRepository('NamiCoreBundle:MenuLink');
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
        // Retrieve page and process it
        if ($page = $this->pageRepo->getPageFromSlug($slug)) {
            $this->processPageBlocks($request, $page);
            // Register analytics
            Analytics::registerPageHit(
                $this->getManager(),
                $request->getClientIp(),
                $request->headers->get('user-agent'),
                $page
            );
        }
        list($response, $page) = $this->processPageResponse($request, $page);
        return $this->renderPage($page, $response);
    }

    public function categoryAction(Request $request, $slug)
    {
        $this->initRepositories();
        $slug = str_replace('.html', '', $slug);
        $category = $this->categoryRepo->getCategoryFromSlug($slug);
        $page = $category ? $category->generatePage() : null;
        list($response, $page) = $this->processPageResponse($request, $page);
        return $this->renderPage($page, $response);
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

    /**
     * @param Request $request
     * @param $page
     */
    protected function processPageBlocks(Request $request, $page)
    {
        foreach ($page->getBlocks() as $block) {
            $event = new BlockRenderEvent($block, $request);
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(BlockRenderEvent::NAME, $event);
        }
    }

    /**
     * @param Request $request
     * @param $page
     * @return array
     */
    protected function processPageResponse(Request $request, $page)
    {
        $response = null;
        if (!$page) {
            $response = new Response();
            $response->setStatusCode(404);
            $page = $this->get404Page();
            return array($response, $page);

        }
        return array($response, $page);
    }

    /**
     * @param $page
     * @param $response
     * @return Response
     */
    protected function renderPage($page, $response)
    {
        return $this->render(
            'NamiCoreBundle:default:layout.html.twig',
            array(
                'menu' => $this->processMenu(),
                'page' => $page
            ),
            $response
        );
    }

    protected function processMenu()
    {
        $links = $this->menuRepo->getMenuTree();

        $factory = new MenuFactory();
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        foreach ($links as $link) {
            $this->processMenuLink($menu, $link);
        }
        return $menu;
    }

    protected function processMenuLink($menuRoot, $link)
    {
        $menuItem = $menuRoot->addChild(
            $link->getName(), [
                'uri' => $link->getLink(),
                'attributes' => [
                    'title' => $link->getTitle()
                ]
            ]
        );
        if (count($link->getItems())) {
            $menuItem->setAttribute('dropdown', true);
            foreach ($link->getItems() as $subLink) {
                $this->processMenuLink($menuItem, $subLink);
            }
        }
    }
}
