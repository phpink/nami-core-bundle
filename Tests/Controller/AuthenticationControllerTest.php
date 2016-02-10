<?php

namespace PhpInk\Nami\CoreBundle\Tests\Controller;

use PhpInk\Nami\CoreBundle\Tests\ApiTestCase;
use Symfony\Component\HttpKernel\Client;

/**
 * JWT Authentication tests
 *
 * @package PhpInk\Nami\CoreBundle\Tests\Controller
 */
class AuthenticationControllerTest extends ApiTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected $loginRoute = 'nami_api_post_user_token';
    protected $profileRoute = 'nami_api_get_user_me';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * test login
     *
     * @covers UserController::postUserTokenAction
     */
    public function testLoginFailure()
    {
        $data = array(
            'username' => 'user',
            'password' => 'userwrongpass',
        );
        $this->client->request(
            'POST',
            $this->getUrl($this->loginRoute),
            $data
        );
        $this->assertJsonResponse($this->client->getResponse(), 401);
    }

    /**
     * test login
     *
     * @covers UserController::postUserTokenAction
     */
    public function testLoginSuccess()
    {
        $data = array(
            'username' => 'manager',
            'password' => 'manager',
        );
        $this->client->request(
            'POST',
            $this->getUrl($this->loginRoute),
            $data
        );
        $this->assertJsonResponse($this->client->getResponse(), 200);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('id', $response['user']);
        $this->assertArrayHasKey('firstName', $response['user']);
        $this->assertArrayHasKey('lastName', $response['user']);

        $this->checkTokenInQueryString($response['token']);
        $this->checkTokenInHeaders($response['token']);
        $this->checkTokenMultipleTimes($response['token']);
        $this->checkBadToken($response['token']);
        $this->checkNoToken($response['token']);
    }

    /**
     * Check that token from query string work
     * @param $token
     */
    public function checkTokenInQueryString($token)
    {

        $client = static::createClient();
        $client->request('HEAD', $this->getUrl($this->profileRoute, array($this->queryParameterName => $token)));

        $this->assertJsonResponse($client->getResponse(), 200, false);
    }

    /**
     * Check that token from Authorization HTTP Header work
     * @param $token
     */
    public function checkTokenInHeaders($token)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $token));
        $client->request('HEAD', $this->getUrl($this->profileRoute));

        $this->assertJsonResponse($client->getResponse(), 200, false);
    }

    /**
     * Check that token works several times, as long as it is valid
     * @param $token
     */
    public function checkTokenMultipleTimes($token)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $token));
        $client->request('HEAD', $this->getUrl($this->profileRoute));

        $this->assertJsonResponse($client->getResponse(), 200, false);
    }

    /**
     * Check that a bad token does not work
     * @param $token
     */
    public function checkBadToken($token)
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $token . 'changed'));
        $client->request('HEAD', $this->getUrl($this->profileRoute));

        $this->assertJsonResponse($client->getResponse(), 401, false);
    }


    /**
     * Check that an error is thrown if no Authorization Header is given
     * @param $token
     */
    public function checkNoToken($token)
    {
        $client = static::createClient();
        $client->request('HEAD', $this->getUrl($this->profileRoute));

        $this->assertJsonResponse($client->getResponse(), 401, false);
    }
}
