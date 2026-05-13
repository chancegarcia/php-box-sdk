<?php

namespace Box\Tests;

use Box\Client;
use Box\ClientConfig;
use Box\Http\Response\BoxResponseInterface;
use Box\Service\ClientServiceRegistry;
use Box\Resource\Collaboration;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Resource\Folder;
use Box\Resource\File;
use Box\Resource\User;
use Box\Resource\Group;
use Box\Exception\BoxException;
use Box\Connection\Token\Token;
use Box\Factory\FolderFactory;
use Box\Service\Folder\FolderService;
use Box\Http\Response\Header\ResponseHeaderInterface;
use Box\Http\Response\Header\StatusLineInterface;
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

    public function testDefaultFactoriesAreInitialized(): void
    {
        $client = new Client();

        $this->assertInstanceOf(Folder::class, $client->getNewFolder());
        $this->assertInstanceOf(File::class, $client->getNewFile());
        $this->assertInstanceOf(User::class, $client->getNewUser());
        $this->assertInstanceOf(Group::class, $client->getNewGroup());
        $this->assertInstanceOf(Collaboration::class, $client->getNewCollaboration());
        $this->assertInstanceOf(TokenInterface::class, $client->getToken());
        $this->assertInstanceOf(ConnectionInterface::class, $client->getConnection());
    }

    public function testInjectedFactoriesAreUsed(): void
    {
        $folderFactory = $this->createMock(FolderFactory::class);
        $folderMock = $this->createMock(Folder::class);
        $folderFactory->expects($this->once())
            ->method('createFolder')
            ->willReturn($folderMock);

        $registry = new ClientServiceRegistry(null, null, null, null, null, null, $folderFactory);
        $client = new Client(null, $registry);
        $result = $client->getNewFolder();

        $this->assertSame($folderMock, $result);
    }

    public function testConstructionWithConfig(): void
    {
        $config = new ClientConfig([
            'clientId' => 'conf_id',
            'clientSecret' => 'conf_secret',
            'deviceName' => 'conf_device'
        ]);

        $client = new Client($config);

        $this->assertEquals('conf_id', $client->getClientId());
        $this->assertEquals('conf_secret', $client->getClientSecret());
        $this->assertEquals('conf_device', $client->getDeviceName());
    }

    public function testAuthenticatedServiceEnforcesToken(): void
    {
        $client = new Client();
        // Client creates empty token by default, but it has no access_token

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Access token is not set for authenticated service');
        // FolderService is an AuthenticatedServiceInterface
        $client->getFolder('123');
    }

    public function testUnknownConfigOptionThrowsException(): void
    {
        $this->expectException(BoxException::class);
        $this->expectExceptionMessage('Unknown configuration option: invalid_key');

        new ClientConfig(['invalid_key' => 'value']);
    }

    public function testBuildAuthorizationUrl()
    {
        $url = $this->client->buildAuthorizationUrl();
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);

        // State can be passed directly to buildAuthorizationUrl
        $url = $this->client->buildAuthorizationUrl(['state' => 'test_state']);
        $this->assertStringContainsString('state=test_state', $url);

        // Dynamic state should override client state
        $url = $this->client->buildAuthorizationUrl(['state' => 'dynamic_state']);
        $this->assertStringContainsString('state=dynamic_state', $url);

        $url = $this->client->buildAuthorizationUrl(['redirect_uri' => 'https://example.com/callback']);
        $this->assertStringContainsString('redirect_uri=' . urlencode('https://example.com/callback'), $url);
    }


    /**
     * @throws JsonException
     * @throws BoxException
     */
    public function testExchangeAuthorizationCodeForTokenSuccess()
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->with(true)->willReturn([
            'access_token' => 'access_foo',
            'refresh_token' => 'refresh_bar',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
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
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRefreshToken')->willReturn('old_refresh');

        $newToken = new Token([
            'access_token' => 'new_access',
            'refresh_token' => 'new_refresh',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ]);

        $this->client->setToken($token);

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->with(true)->willReturn([
            'access_token' => 'new_access',
            'refresh_token' => 'new_refresh',
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $this->client->setConnection($connection);

        $resultToken = $this->client->refreshToken();
        $this->assertNotSame($token, $resultToken);
        $this->assertEquals('new_access', $resultToken->getAccessToken());
    }


    public function testDestroyToken()
    {
        $token = new Token();
        $token->setAccessToken('test_token');

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn(['status' => 'success']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $this->client->setConnection($connection);

        $result = $this->client->destroyToken($token);
        $this->assertEquals(['success' => true], $result);
    }

    protected function createMockResponse(array $data, bool $success = true)
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('json')->willReturn($data);
        $response->method('isSuccessful')->willReturn($success);
        $response->method('isClientError')->willReturn(!$success);
        $response->method('isServerError')->willReturn(false);

        $statusLine = $this->createMock(StatusLineInterface::class);
        $statusLine->method('getStatusCode')->willReturn($success ? 200 : 400);

        $header = $this->createMock(ResponseHeaderInterface::class);
        $header->method('getStatusLine')->willReturn($statusLine);

        $response->method('getResponseHeader')->willReturn($header);
        $response->method('getStatusCode')->willReturn($success ? 200 : 400);

        return $response;
    }

    public function testGetFolderFromBox()
    {
        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => '123',
            'name' => 'Test Folder'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $folder = $this->client->getFolderFromBox('123');
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('123', $folder->getId());
        $this->assertEquals('Test Folder', $folder->getName());
    }

    public function testCreateNewBoxFolder()
    {
        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => '456',
            'name' => 'New Folder'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('post')
            ->with(
                FolderService::ENDPOINT,
                [
                    'name' => 'New Folder',
                    'parent' => ['id' => '123'],
                    'description' => 'A description'
                ],
                true // reverted back to true
            )
            ->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $folder = $this->client->createNewBoxFolder('New Folder', 123, ['description' => 'A description']);
        $this->assertInstanceOf(Folder::class, $folder);
        $this->assertEquals('456', $folder->getId());
    }

    public function testUpdateBoxFolder()
    {
        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => '123',
            'name' => 'Updated Name'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('put')
            ->with(
                FolderService::ENDPOINT . '/123',
                $this->callback(fn($params) => $params['name'] === 'Updated Name'),
                true
            )
            ->willReturn($response);

        $connection->method('addHeader')
            ->willReturnCallback(function ($name, $value) {
                $this->assertEquals('If-Match', $name);
                $this->assertEquals('etag123', $value);
            });

        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
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
        $response->method('json')->with(true)->willReturn([
            'access_token' => 'new_access_token',
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'refresh_token' => 'new_refresh_token'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
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
        $response = $this->createMockResponse([
            'total_count' => 1,
            'entries' => [['type' => 'collaboration', 'id' => 'collab1']]
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
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
        $response = $this->createMockResponse([
            'type' => 'collaboration',
            'id' => 'collab2'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('post')->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
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
        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => '123',
            'shared_link' => ['url' => 'https://box.com/s/foo']
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('put')->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');

        $updatedFolder = $this->client->createSharedLinkForFolder($folder);
        $this->assertInstanceOf(Folder::class, $updatedFolder);
        $this->assertEquals('https://box.com/s/foo', $updatedFolder->getSharedLink()['url']);
    }

    public function testCopyBoxFolder()
    {
        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => 'copy123'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('post')
            ->with(
                $this->anything(),
                $this->anything(),
                true // reverted back to true
            )
            ->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');
        $parent = new Folder();
        $parent->setId('0');

        $copy = $this->client->copyBoxFolder($folder, $parent);
        $this->assertInstanceOf(Folder::class, $copy);
        $this->assertEquals('copy123', $copy->getId());
    }

    public function testGetBoxFolderItems()
    {
        $response = $this->createMockResponse([
            'total_count' => 1,
            'entries' => [['type' => 'file', 'id' => 'file1']]
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $folder = new Folder();
        $folder->setId('123');

        $result = $this->client->getBoxFolderItems($folder);
        $this->assertInstanceOf(Folder::class, $result);
        $this->assertEquals(1, $result->getItemCollection()['total_count']);
    }

    public function testGetFolder()
    {
        // Test id = 0 returns all folders
        // Currently getFolders(true) returns null at the end (line 204 in src/Client.php)

        $token = $this->createMock(TokenInterface::class);
        $token->method('getAccessToken')->willReturn('foo');
        $this->client->setToken($token);

        $response = $this->createMockResponse([
            'type' => 'folder',
            'id' => '0'
        ]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);
        $this->client->setConnection($connection);

        $folders = $this->client->getFolder('0');
        // Capture current behavior: it returns null at line 204 in getFolders
        $this->assertNull($folders);

        // Test caching behavior if we manually populate folders
        $folder = new Folder();
        $folder->setId('123');
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
