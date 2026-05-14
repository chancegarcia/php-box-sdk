<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Enum\UserStatus;
use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\User;
use Box\Service\UserService;
use Box\Tests\Fixtures\BoxApiFixtures;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private function createMockResponse(array $data): BoxResponseInterface
    {
        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $data : (object)$data);
        return $response;
    }

    private function createService(ConnectionInterface $connection): UserService
    {
        $service = new UserService();
        $service->setConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));
        return $service;
    }

    public function testGetCurrentUserReturnsUserResource(): void
    {
        $userData = BoxApiFixtures::userResponse(['id' => '12345', 'name' => 'John Doe', 'login' => 'john@example.com']);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::CURRENT_USER_ENDPOINT)
            ->willReturn($this->createMockResponse($userData));

        $user = $this->createService($connection)->getCurrentUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('12345', $user->getId());
        $this->assertSame('John Doe', $user->getName());
        $this->assertSame('john@example.com', $user->getLogin());
        $this->assertSame('en', $user->getLanguage());
        $this->assertSame(UserStatus::Active, $user->getStatus());
    }

    public function testGetUserByIdReturnsUserResource(): void
    {
        $userId = '17738362';
        $userData = BoxApiFixtures::userResponse(['id' => $userId]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::ENDPOINT . '/' . $userId)
            ->willReturn($this->createMockResponse($userData));

        $user = $this->createService($connection)->getUser($userId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($userId, $user->getId());
        $this->assertSame('Sean Rose', $user->getName());
        $this->assertSame('sean@example.com', $user->getLogin());
        $this->assertSame('Africa/Banjul', $user->getTimezone());
        $this->assertSame(5368709120, $user->getSpaceAmount());
    }

    public function testServiceDoesNotDependOnLegacyUserModel(): void
    {
        $this->assertFalse(class_exists('Box\Model\User\User'), 'Legacy User model should not exist.');
        $this->assertFalse(interface_exists('Box\User\UserInterface'), 'Legacy User interface should not exist.');
    }

    public function testGetUserHandlesErrorResponse(): void
    {
        $errorData = ['type' => 'error', 'status' => 404, 'code' => 'not_found', 'message' => 'User not found'];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn(json_encode($errorData));
        $response->method('json')->willReturnCallback(fn(bool $assoc) => $assoc ? $errorData : (object)$errorData);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);

        $this->expectException(BoxResponseException::class);
        $this->expectExceptionCode(404);

        $this->createService($connection)->getUser('error-user');
    }

    public function testListUsersReturnsArray(): void
    {
        $listData = BoxApiFixtures::userListResponse();

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::ENDPOINT . '?limit=100&offset=0')
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->listUsers();

        $this->assertIsArray($result);
        $this->assertSame(2, $result['total_count']);
        $this->assertCount(2, $result['entries']);
    }

    public function testListUsersRespectsLimitAndOffset(): void
    {
        $listData = BoxApiFixtures::userListResponse([BoxApiFixtures::userResponse()]);

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::ENDPOINT . '?limit=25&offset=50')
            ->willReturn($this->createMockResponse($listData));

        $result = $this->createService($connection)->listUsers(25, 50);

        $this->assertIsArray($result);
    }
}
