<?php

namespace Box\Factory;

use Box\Collaboration\CollaborationInterface;

interface CollaborationFactoryInterface
{
    public function createCollaboration(?array $options = null): CollaborationInterface;
}
