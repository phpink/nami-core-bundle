<?php

namespace PhpInk\Nami\CoreBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * PhpInk API Test Case
 */
abstract class ApiTestCase extends LiipWebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $authorizationHeaderPrefix = 'Bearer';
    protected $queryParameterName = 'bearer';

    /**
     * Test set up:
     * Resets the test database
     */
    public function setUp()
    {
        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update', array('--force' => true));
        $this->runCommand('doctrine:fixtures:load', array(
            //'--purge-with-truncate' => true,
            '--no-interaction' => true
        ));
    }

    public function tearDown()
    {
        $this->runCommand('doctrine:database:drop', array('--force' => true));
        parent::tearDown();
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'admin', $password = 'pass')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->getUrl('nami_api_post_user_token'),
            array(
                'username' => $username,
                'password' => $password,
            )
        );

        $response = $client->getResponse();
        $data     = json_decode($response->getContent(), true);
        $client = static::createClient();
        if ($data && array_key_exists('token', $data)) {
            $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        }
        return $client;
    }

    /**
     * Shortcut method to execute a JSON request.
     *
     * @param Client $client
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function jsonRequest(Client $client, $method, $uri, array $data = array())
    {
        return $client->request(
            $method,
            $uri,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     * @param bool     $checkValidJson
     * @param string   $contentType
     * @return array   Json decoded
     */
    protected function assertJsonResponse(Response $response, $statusCode = 200, $checkValidJson =  true, $contentType = 'application/json')
    {
        $content = null;
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        // If content-type is an URL, check for Location header (DELETE)
        if (strpos($contentType, '://') === false) {
            $this->assertTrue(
                $response->headers->contains('Content-Type', $contentType),
                $response->headers
            );
        } else {
            $this->assertTrue(
                $response->headers->contains('Location', $contentType),
                $response->headers
            );
        }

        if ($checkValidJson) {
            $content = json_decode($response->getContent(), true);
            $this->assertTrue(($content !== null && $content !== false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
        return $content;
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     * @return array   Json decoded
     */
    protected function assertJsonHasResponse(Response $response, $statusCode = 200, $content)
    {
        if (!$content) {
            $content = $this->assertJsonResponse($response, $statusCode);
        }
        $this->assertInternalType('array', $content);
        $this->assertArrayHasKey('elements', $content);
        $this->assertArrayHasKey('count', $content);
        $this->assertArrayHasKey('_links', $content);
        return $content;
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     * @return array   Json decoded
     */
    protected function assertJsonHasPaginationResponse(Response $response, $statusCode = 200)
    {
        $content = $this->assertJsonResponse($response, $statusCode);

        $this->assertJsonHasResponse($response, $statusCode, $content);
        $this->assertArrayHasKey('offset', $content);
        $this->assertArrayHasKey('limit', $content);
        return $content;
    }

    /**
     * Clean data array before sending a request
     * Used because the FOSRest.BodyListener is not called on testing
     * @param array $data
     * @return array Clean data
     */
    public function cleanData(array $data)
    {
        foreach ($data as $key => $item) {
            if ($key === '_references') {
                unset($data['_references']); // Remove extra reference

            } elseif ($key === '_links') {
                unset($data['_links']); // Remove links

            } elseif (is_array($item) || $item instanceof \Traversable) {
                $data[$key] = $this->cleanData($item);
            }
        }
        return $data;
    }
}
