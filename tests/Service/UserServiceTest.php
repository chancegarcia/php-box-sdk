<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Resource\User;
use Box\Service\UserService;
use PHPUnit\Framework\TestCase;
use Box\Connection\Token\TokenInterface;

class UserServiceTest extends TestCase
{
    public function testGetCurrentUserReturnsUserResource(): void
    {
        $userData = [
            'type' => 'user',
            'id' => '12345',
            'name' => 'John Doe',
            'login' => 'john@example.com',
            'status' => 'active'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($userData));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(function (bool $assoc) use ($userData) {
            return $assoc ? $userData : (object)$userData;
        });

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::CURRENT_USER_ENDPOINT)
            ->willReturn($response);

        $service = new UserService();
        $service->setAuthorizedConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $user = $service->getCurrentUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('12345', $user->getId());
        $this->assertSame('John Doe', $user->getName());
        $this->assertSame('john@example.com', $user->getLogin());
    }

    public function testGetUserByIdReturnsUserResource(): void
    {
        $userId = '54321';
        $userData = [
            'type' => 'user',
            'id' => $userId,
            'name' => 'Jane Doe'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($userData));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(function (bool $assoc) use ($userData) {
            return $assoc ? $userData : (object)$userData;
        });

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->once())
            ->method('query')
            ->with(UserService::ENDPOINT . '/' . $userId)
            ->willReturn($response);

        $service = new UserService();
        $service->setAuthorizedConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $user = $service->getUser($userId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($userId, $user->getId());
        $this->assertSame('Jane Doe', $user->getName());
    }

    public function testServiceDoesNotDependOnLegacyUserModel(): void
    {
        $this->assertFalse(class_exists('Box\Model\User\User'), 'Legacy User model should not exist.');
        $this->assertFalse(interface_exists('Box\User\UserInterface'), 'Legacy User interface should not exist.');
    }

    public function testGetUserAcceptsStringId(): void
    {
        $userId = 'string-id-123';
        $userData = [
            'type' => 'user',
            'id' => $userId,
            'name' => 'String ID User'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($userData));
        $response->method('isSuccessful')->willReturn(true);
        $response->method('json')->willReturnCallback(function (bool $assoc) use ($userData) {
            return $assoc ? $userData : (object)$userData;
        });

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);

        $service = new UserService();
        $service->setAuthorizedConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $user = $service->getUser($userId);
        $this->assertSame($userId, $user->getId());
    }

    public function testGetUserHandlesErrorResponse(): void
    {
        $userId = 'error-user';
        $errorData = [
            'type' => 'error',
            'status' => 404,
            'code' => 'not_found',
            'message' => 'User not found'
        ];

        $response = $this->createMock(BoxResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('getStatusCode')->willReturn(404);
        $response->method('getContent')->willReturn(json_encode($errorData));
        $response->method('json')->willReturnCallback(function (bool $assoc) use ($errorData) {
            return $assoc ? $errorData : (object)$errorData;
        });

        $connection = $this->createMock(ConnectionInterface::class);
        $connection->method('query')->willReturn($response);

        $service = new UserService();
        $service->setAuthorizedConnection($connection);
        $service->setToken($this->createMock(TokenInterface::class));

        $this->expectException(BoxResponseException::class);
        $this->expectExceptionCode(404);

        $service->getUser($userId);
    }
}
