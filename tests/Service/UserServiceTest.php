<?php

declare(strict_types=1);

namespace Box\Tests\Service;

use Box\Connection\ConnectionInterface;
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
}
