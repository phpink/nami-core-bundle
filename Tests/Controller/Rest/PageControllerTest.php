<?php

namespace PhpInk\Nami\CoreBundle\Tests\Controller\Rest;

use PhpInk\Nami\CoreBundle\Tests\ApiTestCase;

/**
 * Tests for BrandController
 *
 * @package PhpInk\Nami\CoreBundle\Tests\Controller\Rest
 */
class BrandControllerTest extends ApiTestCase
{

    /**
     * @covers BrandController::getBrandsAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetBrands($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_brands'));

        $response = $client->getResponse();
        $content = $this->assertJsonHasPaginationResponse($response, 200);
        $this->assertCount(10, $content['elements']);

        $firstEl = $content['elements'][0];
        $this->assertArrayHasKey('id', $firstEl);
        $this->assertArrayHasKey('name', $firstEl);
        $this->assertArrayHasKey('logo', $firstEl);
        $this->assertArrayHasKey('slug', $firstEl);
        $this->assertArrayHasKey('locales', $firstEl);
        $this->assertArrayNotHasKey('products', $firstEl);

        // Check brand access
        if ($role !== 'ROLE_MANAGER') {
            // Check all brands are active
            foreach ($content['elements'] as $element) {
                $this->assertArrayNotHasKey('active', $element, "Inactive item found when not ROLE_MANAGER");
            }
        } else  {
            // Search for laguiole inactive brand
            $inactiveFound = false;
            foreach ($content['elements'] as $element) {
                if ($element['slug'] == 'laguiole') {
                    $inactiveFound = true;
                }
            }
            $this->assertTrue($inactiveFound, "Inactive item not found when ROLE_MANAGER");
        }
    }

    /**
     * @covers BrandController::getBrandAction
     *
     * @param array $user user username/password
     * @param string $role user role
     *
     * @dataProvider getUsers
     */
    public function testGetBrand($user, $role)
    {
        // Retrieve a specific id from the get_all
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_brands'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $id = $content['elements'][0]['id'];

        // Real test
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('GET', $this->getUrl('nami_api_get_brand', array('id' => $id)));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);

        $content = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $content);

        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('logo', $content);
        $this->assertArrayHasKey('slug', $content);
        $this->assertArrayNotHasKey('products', $content);

        // Check access
        if ($role !== 'ROLE_MANAGER') {
            $this->assertArrayNotHasKey('active', $content);

            // Retrieve laguiole inactive brand id
            $client = $this->createAuthenticatedClient('manager', 'manager');
            $client->request('GET', $this->getUrl('nami_api_get_brands'));
            $response = $client->getResponse();
            $content = json_decode($response->getContent(), true);
            $inactiveId = null;
            foreach ($content['elements'] as $element) {
                if ($element['slug'] == 'laguiole') {
                    $inactiveId = $element['id'];
                }
            }
            $this->assertTrue(($inactiveId > 0), "Inactive item not found when ROLE_MANAGER");

            // Check access is restricted
            $client = $this->createAuthenticatedClient($user['username'], $user['password']);
            $client->request('GET', $this->getUrl('nami_api_get_brand', array('id' => $inactiveId)));

            $response = $client->getResponse();
            $this->assertJsonResponse($response, 500);
            $content = json_decode($response->getContent(), true);
            $this->assertInternalType('array', $content);

            $this->assertArrayHasKey('message', $content);
            $this->assertEquals($content['message'], "Entity is inactive.");

        }
    }

    /**
     * @covers BrandController::postBrandsAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testPostBrand($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $brand = array(
            "name" => "Technics",
            "active" => true,
            "locales" => array(
                "fr" => array(
                    "id" => 1,
                    "description" => "Technics est une entreprise japonaise de matériel électronique, de HiFi et d'instruments de musique, fondée en 1965.",
                    "priceLabel" => null
                ),
                "en" => array(
                    "id" => 1,
                    "description" => "Technics is the brand of hi-fi audio products such as amplifiers,network audio players, speaker systems and music system solutions.",
                    "priceLabel" => null
                )
            ),
            "logo" => null,
            "supplier" => 1
        );
        $brand = $this->cleanData($brand);
        $client->request('POST', $this->getUrl('nami_api_post_brands'), $brand);

        $response = $client->getResponse();

        if ($role === 'ROLE_MANAGER') {
            $this->assertJsonResponse($response, 201, false);
        } else {
            $this->assertJsonResponse($response, 403);
        }
    }

    /**
     * @covers BrandController::putBrandAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testPutBrand($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);

        // Retrieve the first brand
        $client->request('GET', $this->getUrl('nami_api_get_brands'));
        $content = json_decode($client->getResponse()->getContent(), true);
        $brand = $content['elements'][0];

        // Update some properties
        $brand['name'] = 'The test brand';
        $brand['locales']['fr']['description'] = 'Test brand locale FR';
        $brand['locales']['en'] = array('description' => 'Test brand locale EN');
        $brand = $this->cleanData($brand);

        $client->request(
            'PUT', $this->getUrl(
                'nami_api_put_brand',
                array('id' => $brand['id'])
            ), $brand
        );

        $response = $client->getResponse();

        if ($role === 'ROLE_RESELLER') {
            $this->assertJsonResponse($response, 403); // Access denied

        } elseif ($role === 'ROLE_MANAGER') {
            $output = $this->assertJsonResponse($response, 200);
            $this->assertEquals('The test brand', $output['name']);
            $this->assertEquals('the-test-brand', $output['slug']);
            $this->assertEquals('Test brand locale FR', $output['locales']['fr']['description']);
            $this->assertEquals('Test brand locale EN', $output['locales']['en']['description']);

        } else {
            $this->assertJsonResponse($response, 403); // Access denied
        }
    }

    /**
     * @covers BrandController::deleteBrandAction
     *
     * @param array $user
     *
     * @dataProvider getUsers
     */
    public function testDeleteBrand($user, $role)
    {
        $client = $this->createAuthenticatedClient($user['username'], $user['password']);
        $client->request('DELETE', $this->getUrl('nami_api_delete_brand', array('id' => 14)));

        $response = $client->getResponse();

        if ($role === 'ROLE_RESELLER') {
            $this->assertJsonResponse($response, 403); // Access denied
        } elseif ($role === 'ROLE_MANAGER') {
            $this->assertJsonResponse($response, 204, false, $this->getUrl('nami_api_get_brands', array(), true)); // Brand delete OK
        } else {
            $this->assertJsonResponse($response, 404); // Brand not exits (but access denied was expected)
        }
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return array(
            array(array('username' => 'reseller', 'password' => 'reseller'), 'ROLE_RESELLER'),
            array(array('username' => 'manager', 'password' => 'manager'), 'ROLE_MANAGER'),
            array(array('username' => 'supplier', 'password' => 'supplier'), 'ROLE_SUPPLIER')
        );
    }
}
