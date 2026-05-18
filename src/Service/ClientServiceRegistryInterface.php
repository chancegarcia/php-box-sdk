<?php

declare(strict_types=1);

namespace Box\Service;

use Box\Factory\CollaborationFactory;
use Box\Factory\ConnectionFactoryInterface;
use Box\Factory\FileFactory;
use Box\Factory\FolderFactory;
use Box\Factory\GroupFactory;
use Box\Factory\TokenFactoryInterface;
use Box\Factory\UserFactory;
use Box\Service\Collaboration\CollaborationServiceInterface;
use Box\Service\File\FileServiceInterface;
use Box\Service\Folder\FolderServiceInterface;
use Box\Service\Group\GroupServiceInterface;

interface ClientServiceRegistryInterface
{
    public function getFolderService(): FolderServiceInterface;
    public function getFileService(): FileServiceInterface;
    public function getUserService(): UserServiceInterface;
    public function getGroupService(): GroupServiceInterface;
    public function getCollaborationService(): CollaborationServiceInterface;
    public function getSearchService(): SearchServiceInterface;
    public function getFolderFactory(): FolderFactory;
    public function getFileFactory(): FileFactory;
    public function getUserFactory(): UserFactory;
    public function getGroupFactory(): GroupFactory;
    public function getCollaborationFactory(): CollaborationFactory;
    public function getTokenFactory(): TokenFactoryInterface;
    public function getConnectionFactory(): ConnectionFactoryInterface;
}
