<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Factory\CollaborationFactory;
use Box\Factory\ConnectionFactory;
use Box\Factory\ConnectionFactoryInterface;
use Box\Factory\FileFactory;
use Box\Factory\FolderFactory;
use Box\Factory\GroupFactory;
use Box\Factory\TokenFactory;
use Box\Factory\TokenFactoryInterface;
use Box\Factory\UserFactory;
use Box\Service\Folder\FolderService;
use Box\Service\Folder\FolderServiceInterface;

class ClientServiceRegistry implements ClientServiceRegistryInterface
{
    protected FolderServiceInterface $folderService;
    protected FolderFactory $folderFactory;
    protected FileFactory $fileFactory;
    protected UserFactory $userFactory;
    protected GroupFactory $groupFactory;
    protected CollaborationFactory $collaborationFactory;
    protected TokenFactoryInterface $tokenFactory;
    protected ConnectionFactoryInterface $connectionFactory;

    public function __construct(
        ?FolderServiceInterface $folderService = null,
        ?FolderFactory $folderFactory = null,
        ?FileFactory $fileFactory = null,
        ?UserFactory $userFactory = null,
        ?GroupFactory $groupFactory = null,
        ?CollaborationFactory $collaborationFactory = null,
        ?TokenFactoryInterface $tokenFactory = null,
        ?ConnectionFactoryInterface $connectionFactory = null
    ) {
        $this->folderService = $folderService ?? new FolderService();
        $this->folderFactory = $folderFactory ?? new FolderFactory();
        $this->fileFactory = $fileFactory ?? new FileFactory();
        $this->userFactory = $userFactory ?? new UserFactory();
        $this->groupFactory = $groupFactory ?? new GroupFactory();
        $this->collaborationFactory = $collaborationFactory ?? new CollaborationFactory();
        $this->tokenFactory = $tokenFactory ?? new TokenFactory();
        $this->connectionFactory = $connectionFactory ?? new ConnectionFactory();
    }

    public function getFolderService(): FolderServiceInterface
    {
        return $this->folderService;
    }

    public function getFolderFactory(): FolderFactory
    {
        return $this->folderFactory;
    }

    public function getFileFactory(): FileFactory
    {
        return $this->fileFactory;
    }

    public function getUserFactory(): UserFactory
    {
        return $this->userFactory;
    }

    public function getGroupFactory(): GroupFactory
    {
        return $this->groupFactory;
    }

    public function getCollaborationFactory(): CollaborationFactory
    {
        return $this->collaborationFactory;
    }

    public function getTokenFactory(): TokenFactoryInterface
    {
        return $this->tokenFactory;
    }

    public function getConnectionFactory(): ConnectionFactoryInterface
    {
        return $this->connectionFactory;
    }
}
