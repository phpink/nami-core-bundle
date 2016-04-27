<?php

namespace PhpInk\Nami\CoreBundle\Tests\Controller\Rest;

use PhpInk\Nami\CoreBundle\Tests\ApiTestCase;

/**
 * Tests for PageController
 *
 * @package PhpInk\Nami\CoreBundle\Tests\Controller\Rest
 */
class PageControllerTest extends ApiTestCase
{

    /**
     * @covers PageController::getPagesAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetPages($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_pages'));

        $response = $client->getResponse();
        if ($role === 'ROLE_ADMIN') {
            $content = $this->assertJsonHasPaginationResponse($response, 200);
            $this->assertCount(4, $content['elements']);

            $firstEl = $content['elements'][0];
            $this->assertArrayHasKey('id', $firstEl);
            $this->assertArrayHasKey('title', $firstEl);
            $this->assertArrayHasKey('slug', $firstEl);
            $this->assertArrayHasKey('header', $firstEl);
            $this->assertArrayHasKey('content', $firstEl);
            $this->assertArrayNotHasKey('foo', $firstEl);
            
        } else {
            $this->assertJsonResponse($response, 401);
        }
    }

    /**
     * @covers PageController::getPageAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetPage($user, $role)
    {
        // Retrieve a specific id from the get_all
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_pages'));
        $response = $client->getResponse();

        if ($role === 'ROLE_ADMIN') {
            $content = json_decode($response->getContent(), true);
            $id = $content['elements'][0]['id'];

            // Real test
            $client = $this->createAuthenticatedClient($user['username'], $user['password']);
            $client->request('GET', $this->getUrl('nami_api_get_page', array('id' => $id)));

            $response = $client->getResponse();
            $this->assertJsonResponse($response, 200);

            $content = json_decode($response->getContent(), true);
            $this->assertInternalType('array', $content);

            $this->assertArrayHasKey('title', $content);
            $this->assertArrayHasKey('slug', $content);
            $this->assertArrayHasKey('header', $content);
            $this->assertArrayHasKey('content', $content);
            $this->assertArrayNotHasKey('foo', $content);

        } else {
            $this->assertJsonResponse($response, 401);
        }
    }

    /**
     * @covers PageController::postPagesAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testPostPage($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $page = array(
            'title' => 'Nami CMS Demo',
            'slug' => '/demo',
            'header' => 'NAMI <strong>CMS</strong> demo app',
            'metaKeywords' => "nami, cms, symfony",
            'metaDescription' => "Nami, a basic Content management system for Symfony",
            'background' => null,
            'category' => null,
            'backgroundColor' => null,
            'borderColor' => null,
            'footerColor' => null,
            'negativeText' => false,
            'blocks' => array(
                array(
                    'title' => 'Nami CMS',
                    'content' => '<p><span itemprop="description">Content management system</span> with Symfony 2.7</p>',
                    'template' => 'front',
                    'images' => array(),
                    'type' => 'default',
                ),
            ),
        );
        $page = $this->cleanData($page);
        $client->request('POST', $this->getUrl('nami_api_post_pages'), $page);

        $response = $client->getResponse();

        if ($role === 'ROLE_ADMIN') {
            $this->assertJsonResponse($response, 201, false);
        } else {
            $this->assertJsonResponse($response, 401);
        }
    }

    /**
     * @covers PageController::putPageAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     * @group test
     */
    public function testPutPage($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);

        // Retrieve the first Page
        $client->request('GET', $this->getUrl('nami_api_get_pages'));

        if ($role === 'ROLE_ADMIN') {
            $content = json_decode($client->getResponse()->getContent(), true);
            $page = $content['elements'][0];

            // Update some properties
            $page['title'] = 'The test Page';
            $page['header'] = 'Nami CMS DEMO';
            $page = $this->cleanData($page);


            $client->request(
                'PUT', $this->getUrl(
                    'nami_api_put_page',
                    array('id' => $page['id'])
                ), $page
            );

            $response = $client->getResponse();
            $output = $this->assertJsonResponse($response, 200);
            $this->assertEquals('The test Page', $output['title']);
            $this->assertEquals('the-test-Page', $output['slug']);
            $this->assertEquals('Nami CMS DEMO', $output['header']);

        } else {
            $this->assertJsonResponse($client->getResponse(), 401); // Access denied
        }
    }

    /**
     * @covers PageController::deletePageAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testDeletePage($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        // Retrieve the first Page
        $client->request('GET', $this->getUrl('nami_api_get_pages'));

        if ($role === 'ROLE_ADMIN') {
            $content = json_decode($client->getResponse()->getContent(), true);
            $page = $content['elements'][0];
            $client->request('DELETE', $this->getUrl('nami_api_delete_page', array('id' => $page['id'])));
            $this->assertJsonResponse($client->getResponse(), 204, false, $this->getUrl('nami_api_get_pages', array(), true)); // Page delete OK

        } else {
            $this->assertJsonResponse($client->getResponse(), 401); // Access denied
        }
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return array(
            array(array('username' => 'admin', 'password' => 'pass'), 'ROLE_ADMIN'),
            array(array('username' => 'admin', 'password' => 'wrongpass'), 'ROLE_WRONG'),
        );
    }
}
