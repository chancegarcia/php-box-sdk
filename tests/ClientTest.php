<?php

namespace Box\Tests;

use Box\Client;
use Box\Http\Response\BoxResponseInterface;
use Box\Connection\Connection;
use Box\Connection\Token\Token;
use Box\Folder\Folder;
use Box\Collaboration\Collaboration;
use Box\User\User;
use Box\Group\Group;
use Box\Exception\BoxException;
use JsonException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client();
        $this->client->setClientId('test_client_id');
        $this->client->setClientSecret('test_client_secret');
    }

    public function testBuildAuthQuery()
    {
        $url = $this->client->buildAuthQuery();
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);

        $this->client->setState('test_state');
        $url = $this->client->buildAuthQuery();
        $this->assertStringContainsString('state=test_state', $url);

        $this->client->setRedirectUri('https://example.com/callback');
        $url = $this->client->buildAuthQuery();
        $this->assertStringContainsString('redirect_uri=' . urlencode('https://example.com/callback'), $url);
    }

    public function testAuth()
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('query')
            ->with($this->client->buildAuthQuery())
            ->willReturn($this->createMock(BoxResponseInterface::class));

        $this->client->setConnection($connection);
        $this->client->auth();
    }

    /**
     * @throws JsonException
     * @throws BoxException
     */
    public function testGetAccessTokenSuccess()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'access_token' => 'access_foo',
            'refresh_token' => 'refresh_bar',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ], JSON_THROW_ON_ERROR));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $this->client->setConnection($connection);
        $this->client->setAuthorizationCode('test_code');

        $token = $this->client->exchangeAuthorizationCodeForToken();
        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('access_foo', $token->getAccessToken());
        $this->assertEquals('refresh_bar', $token->getRefreshToken());
        $this->assertNotNull($token->getReceivedAt());
    }

    public function testRefreshTokenSuccess(): void
    {
        $token = new Token();
        $token->setRefreshToken('old_refresh');
        $this->client->setToken($token);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'access_token' => 'new_access',
            'refresh_token' => 'new_refresh',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ], JSON_THROW_ON_ERROR));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $this->client->setConnection($connection);

        $newToken = $this->client->refreshToken();
        $this->assertSame($token, $newToken);
        $this->assertEquals('new_access', $token->getAccessToken());
        $this->assertEquals('new_refresh', $token->getRefreshToken());
        $this->assertNotNull($token->getReceivedAt());
    }

    public function testGetAuthorizationHeader()
    {
        $token = new Token();
        $token->setAccessToken('test_token');
        $this->client->setToken($token);

        $header = $this->client->getAuthorizationHeader();
        $this->assertEquals('Authorization: Bearer test_token', $header);
    }

    public function testSetConnectionAuthHeader()
    {
        $token = new Token();
        $token->setAccessToken('test_token');
        $this->client->setToken($token);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('setCurlOpts')
            ->with($this->callback(function ($opts) {
                return isset($opts['CURLOPT_HTTPHEADER']) &&
                       in_array('Authorization: Bearer test_token', $opts['CURLOPT_HTTPHEADER']) &&
                       in_array('X-Extra: foo', $opts['CURLOPT_HTTPHEADER']);
            }));

        $this->client->setConnectionAuthHeader($connection, ['X-Extra: foo']);
        $this->assertTrue(true); // satisfy risky test
    }

    public function testDestroyToken()
    {
        $token = new Token();
        $token->setAccessToken('test_token');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode(['status' => 'success']));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $this->client->setConnection($connection);

        $result = $this->client->destroyToken($token);
        $this->assertEquals(['status' => 'success'], $data = json_decode(json_encode($result), true));
    }

    public function testGetFolderFromBox()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => '123',
            'name' => 'Test Folder'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = $this->client->getFolderFromBox('123');
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('123', $folder->getId());
        $this->assertEquals('Test Folder', $folder->getName());
    }

    public function testCreateNewBoxFolder()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => '456',
            'name' => 'New Folder'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                Folder::URI,
                [
                    'name' => 'New Folder',
                    'parent' => ['id' => '123'],
                    'description' => 'A description'
                ],
                true
            )
            ->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = $this->client->createNewBoxFolder('New Folder', 123, ['description' => 'A description']);
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('456', $folder->getId());
    }

    public function testUpdateBoxFolder()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => '123',
            'name' => 'Updated Name'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                Folder::URI . '/123',
                $this->callback(fn($params) => $params['name'] === 'Updated Name'),
                true
            )
            ->willReturn($response);

        $connection->expects($this->once())
            ->method('addHeader')
            ->with('If-Match', 'etag123');

        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');
        $folder->setName('Updated Name');
        $folder->setEtag('etag123');

        $data = $this->client->updateBoxFolder($folder, true);
        $this->assertIsArray($data);
        $this->assertEquals('Updated Name', $data['name']);
    }

    public function testExchangeAuthorizationCodeForToken()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'access_token' => 'new_access_token',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'refresh_token' => 'new_refresh_token'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('post')->willReturn($response);
        $this->client->setConnection($connection);

        $this->client->setClientId('client_id');
        $this->client->setClientSecret('client_secret');
        $this->client->setAuthorizationCode('auth_code');

        $token = $this->client->exchangeAuthorizationCodeForToken();
        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals('new_access_token', $token->getAccessToken());
    }

    public function testGetFolderCollaborations()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'total_count' => 1,
            'entries' => [['type' => 'collaboration', 'id' => 'collab1']]
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');

        $data = $this->client->getFolderCollaborations($folder);
        $this->assertIsArray($data);
        $this->assertEquals(1, $data['total_count']);
        $this->assertEquals('collab1', $data['entries'][0]['id']);
    }

    public function testAddCollaboration()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'collaboration',
            'id' => 'collab2'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('post')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');
        $user = new User();
        $user->setId('user1');

        $collab = $this->client->addCollaboration($folder, $user, 'editor');
        $this->assertInstanceOf(Collaboration::class, $collab);
    }

    public function testCreateSharedLinkForFolder()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => '123',
            'shared_link' => ['url' => 'https://box.com/s/foo']
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('put')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');

        $updatedFolder = $this->client->createSharedLinkForFolder($folder);
        $this->assertInstanceOf(Folder::class, $updatedFolder);
        $this->assertEquals('https://box.com/s/foo', $updatedFolder->getSharedLink()['url']);
    }

    public function testCopyBoxFolder()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => 'copy123'
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('post')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');
        $parent = new Folder();
        $parent->setId('0');

        // Mocking getFolderFromBox to avoid its side effects if it calls connection
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['getFolderFromBox'])
            ->getMock();
        $client->setConnection($connection);
        $client->setToken($token);
        $client->setClientId('foo');
        $client->setClientSecret('bar');
        $client->method('getFolderFromBox')->willReturn(new Folder(['id' => 'copy123']));

        $copy = $client->copyBoxFolder($folder, $parent);
        $this->assertInstanceOf(Folder::class, $copy);
        $this->assertEquals('copy123', $copy->getId());
    }

    public function testGetBoxFolderItems()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'total_count' => 1,
            'entries' => [['type' => 'file', 'id' => 'file1']]
        ]));

        $connection = $this->createMock(Connection::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');

        $result = $this->client->getBoxFolderItems($folder);
        $this->assertSame($folder, $result);
        $this->assertEquals(1, $folder->getItemCollection()['total_count']);
    }

    public function testGetFolder()
    {
        // Test id = 0 returns all folders
        // Currently getFolders(true) returns null at the end (line 204 in src/Client.php)

        $token = new Token();
        $token->setAccessToken('foo');
        $this->client->setToken($token);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode([
            'type' => 'folder',
            'id' => '0'
        ]));
        $connection = $this->createMock(Connection::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $folders = $this->client->getFolder('0');
        // Capture current behavior: it returns null at line 204 in getFolders
        $this->assertNull($folders);

        // Test caching behavior if we manually populate folders
        $folder = new Folder(['id' => '123']);
        $this->client->setFolders(['123' => $folder]);
        $this->assertSame($folder, $this->client->getFolder('123', false));
    }

    public function testIsTokenExpired()
    {
        $client = new Client();
        $this->assertFalse($client->isTokenExpired());

        $token = $this->createMock(Token::class);
        $token->method('isExpired')->willReturn(true);
        $client->setToken($token);
        $this->assertTrue($client->isTokenExpired());

        $token = $this->createMock(Token::class);
        $token->method('isExpired')->willReturn(false);
        $client->setToken($token);
        $this->assertFalse($client->isTokenExpired());
    }

    public function testGetRemainingTokenLifetime()
    {
        $client = new Client();
        $this->assertNull($client->getRemainingTokenLifetime());

        $token = $this->createMock(Token::class);
        $token->method('getExpiresIn')->willReturn(3600);
        $token->method('getReceivedAt')->willReturn(time() - 600);
        $client->setToken($token);

        $remaining = $client->getRemainingTokenLifetime();
        $this->assertGreaterThan(2900, $remaining);
        $this->assertLessThanOrEqual(3000, $remaining);

        // Test clamping to 0
        $token = $this->createMock(Token::class);
        $token->method('getExpiresIn')->willReturn(3600);
        $token->method('getReceivedAt')->willReturn(time() - 4000);
        $client->setToken($token);
        $this->assertEquals(0, $client->getRemainingTokenLifetime());

        // Test null if missing metadata
        $token = $this->createMock(Token::class);
        $token->method('getExpiresIn')->willReturn(null);
        $client->setToken($token);
        $this->assertNull($client->getRemainingTokenLifetime());
    }
}
