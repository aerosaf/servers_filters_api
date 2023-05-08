<?php

namespace App\Repository;

use App\Entity\Server;
use App\Entity\ServerFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class ServerRepository
{
    private $servers;
    private $filePath;
    const COLUMN_MODEL = 'A';
    const COLUMN_RAM = 'B';
    const COLUMN_HDD = 'C';
    const COLUMN_LOCATION = 'D';
    const COLUMN_PRICE = 'E';

    public function __construct(KernelInterface $kernel)
    {
        $this->filePath = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'LeaseWeb_servers_filters_assignment.xlsx';
        $this->servers = $this->getServers($kernel);
    }

    public function getServers(KernelInterface $kernel): array
    {
        $absolutePath = $kernel->getProjectDir() . $this->filePath;
        $spreadsheet = IOFactory::load($absolutePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $server = new Server;
            $server->setModel($worksheet->getCell(self::COLUMN_MODEL . $row->getRowIndex())->getValue());
            $server->setRam($worksheet->getCell(self::COLUMN_RAM . $row->getRowIndex())->getValue());
            $server->setHdd($worksheet->getCell(self::COLUMN_HDD . $row->getRowIndex())->getValue());
            $server->setLocation($worksheet->getCell(self::COLUMN_LOCATION . $row->getRowIndex())->getValue());
            $server->setPrice($worksheet->getCell(self::COLUMN_PRICE . $row->getRowIndex())->getValue());
            $servers[] = $server;
        }

        return $servers;
    }

    public function filterServers(ServerFilter $filters): array
    {
        $filteredServers = $this->servers;
        $filteredServers = $this->filterByStorage($filteredServers, $filters->minStorage, $filters->maxStorage);
        $filteredServers = $this->filterByRam($filteredServers, $filters->ram);
        $filteredServers = $this->filterByHarddiskType($filteredServers, $filters->harddiskType);
        $filteredServers = $this->filterByLocation($filteredServers, $filters->location);

        return $filteredServers;
    }

    public function getLocationOptions(): array
    {
        $locations =  array_map(function ($server) {
            return $server->getLocation();
        }, $this->servers);

        return array_unique($locations);
    }

    private function filterByStorage(array $servers, int $minStorage, int $maxStorage): array
    {
        if ($maxStorage === 0 || $minStorage === 0) {
            return $servers;
        }
        return array_filter($servers, function ($server) use ($minStorage, $maxStorage) {
            $serverStorage = $server->getStorage();
            return $serverStorage >= $minStorage && $serverStorage <= $maxStorage;
        });
    }

    private function filterByRam(array $servers, string $ram): array
    {
        if ($ram === "") {
            return $servers;
        }
        $ramValues = explode(',', $ram);
        return array_filter($servers, function ($server) use ($ramValues) {
            foreach ($ramValues as $values) {
                if (preg_match('/^' . $values . '/', $server->getRam())) {
                    return $server;
                }
            }
        });
    }

    public function filterByHarddiskType(array $servers, ?string $harddiskType): array
    {
        if ($harddiskType === "") {
            return $servers;
        }

        return array_filter($servers, function (Server $server) use ($harddiskType) {
            return $server->getHardDiskType() === $harddiskType;
        });
    }

    private function filterByLocation(array $servers, string $location): array
    {
        if ($location === "") {
            return $servers;
        }
        return array_filter($servers, function ($server) use ($location) {
            return $server->getLocation() === urldecode($location);
        });
    }
}