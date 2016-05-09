<?php

namespace PhpInk\Nami\CoreBundle\Tests\Controller\Rest;

use PhpInk\Nami\CoreBundle\Tests\ApiTestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Tests for CategoryController
 *
 * @package PhpInk\Nami\CoreBundle\Tests\Controller\Rest
 */
class CategoryControllerTest extends ApiTestCase
{

    /**
     * @covers CategoryController::getCategoriesAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetCategories($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_categories'));

        $response = $client->getResponse();
        if ($role === 'ROLE_ADMIN') {
            $content = $this->assertJsonHasPaginationResponse($response, 200);
            $this->assertCount(4, $content['elements']);

            $firstEl = $content['elements'][0];
            $this->assertArrayHasKey('id', $firstEl);
            $this->assertArrayHasKey('name', $firstEl);
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
     * @covers CategoryController::getCategoryAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetCategory($user, $role)
    {
        // Retrieve a specific id from the get_all
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_categories'));
        $response = $client->getResponse();

        if ($role === 'ROLE_ADMIN') {
            $content = json_decode($response->getContent(), true);
            $id = $content['elements'][0]['id'];

            // Real test
            $client = $this->createAuthenticatedClient($user['username'], $user['password']);
            $client->request('GET', $this->getUrl('nami_api_get_category', array('id' => $id)));

            $response = $client->getResponse();
            $this->assertJsonResponse($response, 200);

            $content = json_decode($response->getContent(), true);
            $this->assertInternalType('array', $content);

            $this->assertArrayHasKey('name', $content);
            $this->assertArrayHasKey('slug', $content);
            $this->assertArrayHasKey('title', $content);
            $this->assertArrayHasKey('header', $content);
            $this->assertArrayHasKey('content', $content);
            $this->assertArrayNotHasKey('foo', $content);

        } else {
            $this->assertJsonResponse($response, 401);
        }
    }

    /**
     * @covers CategoryController::postCategoriesAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     * @group category
     */
    public function testPostCategory($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $category = [
            'position' =>  3,
            'parent' => null,
            'name' => 'Test 2',
            'title' => 'Test 2 category',
            'header' => 'Test 2',
            'metaKeywords' => 'test2,category,db',
            'metaDescription' => 'Test 2 category SEO',
            'content' => '<p>Test 2 category description</p>>'
        ];
        $client->request('POST', $this->getUrl('nami_api_post_categories'), $category);

        $response = $client->getResponse();

        if ($role === 'ROLE_ADMIN') {
            $this->assertJsonResponse($response, 201, false);
        } else {
            $this->assertJsonResponse($response, 401);
        }
    }

    /**
     * @covers CategoryController::putCategoryAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     * @group category
     */
    public function testPutCategory($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);

        // Retrieve the first Category
        $client->request('GET', $this->getUrl('nami_api_get_categories'));

        if ($role === 'ROLE_ADMIN') {
            $content = json_decode($client->getResponse()->getContent(), true);
            $category = $content['elements'][0];

            // Update some properties
            $category['name'] = 'The test 3 Category!';
            $category['header'] = 'Nami CMS DEMO';
            $category = $this->cleanData($category);


            $client->request(
                'PUT', $this->getUrl(
                    'nami_api_put_category',
                    array('id' => $category['id'])
                ), $category
            );

            $response = $client->getResponse();
            $output = $this->assertJsonResponse($response, 200);
            $this->assertEquals('The test 3 Category!', $output['name']);
            $this->assertEquals('the-test-3-category', $output['slug']);
            $this->assertEquals('Nami CMS DEMO', $output['header']);

        } else {
            $this->assertJsonResponse($client->getResponse(), 401); // Access denied
        }
    }

    /**
     * @covers CategoryController::deleteCategoryAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testDeleteCategory($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        // Retrieve the first Category
        $client->request('GET', $this->getUrl('nami_api_get_categories'));

        if ($role === 'ROLE_ADMIN') {
            // test response & redirection
            $content = json_decode($client->getResponse()->getContent(), true);
            $category = $content['elements'][0];
            $client->request('DELETE', $this->getUrl('nami_api_delete_category', array('id' => $category['id'])));
            $this->assertJsonResponse($client->getResponse(), 204, false, $this->getUrl('nami_api_get_categories', [], UrlGenerator::ABSOLUTE_URL)); // Category delete OK
            
            // test that the category has been deleted
            $client->request('GET', $this->getUrl('nami_api_get_category', array('id' => $category['id'])));
            $this->assertJsonResponse($client->getResponse(), 404); // Not found

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
