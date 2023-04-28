<?php

namespace App\Service;

use App\Entity\ServerFilter;
use App\Repository\ServerRepository;

class ServerService
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function getServersExcel(ServerFilter $filter): array
    { 
        return $this->serverRepository->filterServers($filter);
    }

    public function getLocationOptions(): array
    {
        return $this->serverRepository->getLocationOptions();
    }

    public function getServersDb(ServerFilter $filter): array
    {
        return $this->serverRepository->findAllDb($filter);
    }
}
