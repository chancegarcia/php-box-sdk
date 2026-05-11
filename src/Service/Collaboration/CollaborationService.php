<?php

namespace Box\Service\Collaboration;

use Box\Service\AuthenticatedServiceInterface;
use Box\Service\Service;

class CollaborationService extends Service implements AuthenticatedServiceInterface
{
    public const ENDPOINT = "https://api.box.com/2.0/collaborations";
}
