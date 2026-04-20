<?php
/**
 * Created by JetBrains PhpStorm.
 * User: chance
 * Date: 7/23/13
 * Time: 2:19 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BoxTest;
use Box\Model\Client\Client;
use Box\Model\Connection\Token\Token;
use Box\Model\Group\Group;
use PHPUnit\Framework\TestCase;

class BoxClientTest extends TestCase
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Client
     */
    protected $clientWithToken;

    protected function setUp(): void
    {
        parent::setUp();

        // $mock = $this->getMockBuilder(Connection::class)->setMethods(['initCurl', 'initCurlOpts'])->getMock();

        $this->client = new Client();
        $this->client->setClientId('foobar');
        $this->client->setClientSecret('kittens');

        $this->clientWithToken = new Client();
        $this->clientWithToken->setClientId('baz');
        $this->clientWithToken->setClientSecret('bunnies');
        $token = new Token();
        $token->setAccessToken('DY994XYpCLJ0rErndKZEbYAcS85VsNHB');
        $this->clientWithToken->setToken($token);


    }

    public function testBuildAuthQuery()
    {
        // test string return
        // base string
        $this->assertStringContainsString('response_type', $this->client->buildAuthQuery());
        $this->assertStringContainsString('client_id', $this->client->buildAuthQuery());

        // with state
        $this->client->setState('a:b:c:1');
        $this->assertStringContainsString('state', $this->client->buildAuthQuery());

        // with redirect uri
        $this->client->setRedirectUri('http://example.com');
        $this->assertStringContainsString('redirect_uri', $this->client->buildAuthQuery());
    }

    // test get token

    // test build auth calls get client id
    // test build auth calls get state
    // test build auth calls build query
    // test build auth calls get redirect uri

    // test get connection

    // test auth calls connection query

    // test refresh

    public function testRefreshToken()
    {

    }

    public function testGetAccessToken()
    {
        // connection
        // auth code
    }

    public function testUploadFileToBox()
    {

    }

    public function testClient()
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testClientToken()
    {
        $token = $this->clientWithToken->getToken();

        $this->assertInstanceOf(Token::class, $token);
    }
}
