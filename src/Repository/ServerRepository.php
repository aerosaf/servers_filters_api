<?php
namespace App\Repository;

use App\Entity\Server;
use App\Entity\ServerFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class ServerRepository
{
    private $servers;

    public function __construct(KernelInterface $kernel)
    {
        $this->servers = self::getServers($kernel);
    }

    public function getServers($kernel): array
    {
        $absolutePath = $kernel->getProjectDir() . '/public/LeaseWeb_servers_filters_assignment.xlsx';
        $spreadsheet = IOFactory::load($absolutePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $server = new Server;
            $server->setModel($worksheet->getCell('A' . $row->getRowIndex())->getValue());
            $server->setRam($worksheet->getCell('B' . $row->getRowIndex())->getValue());
            $server->setHdd($worksheet->getCell('C' . $row->getRowIndex())->getValue());
            $server->setLocation($worksheet->getCell('D' . $row->getRowIndex())->getValue());
            $server->setPrice($worksheet->getCell('E' . $row->getRowIndex())->getValue());
            $servers[] = $server;
        }

        return $servers;
    }

    public function filterServers(ServerFilter $filters): array
    {
        $filteredServers = $this->servers;

        // filter by storage
        $minStorage = $filters->minStorage;
        $maxStorage = $filters->maxStorage;
        if ($maxStorage && $maxStorage) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($minStorage, $maxStorage) {
                $serverStorage = $server->getStorage();
                return $serverStorage >= $minStorage && $serverStorage <= $maxStorage;
            });
        }

        // filter by RAM
        $ram = $filters->ram;
        if ($ram) {
            $ramValues = explode(',', $ram);
            $filteredServers = array_filter($filteredServers, function ($server) use ($ramValues) {
                foreach ($ramValues as $values) {
                    if (preg_match('/^' . $values . '/', $server->getRam())) {
                        return $server;
                    }
                }
            });
        }

        // filter by harddisk type
        $harddiskType = $filters->harddiskType;
        if ($harddiskType) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($harddiskType) {
                return $server->getHarddiskType() === $harddiskType;
            });
        }

        // filter by location
        $location = $filters->location;
        if ($location) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($location) {
                return $server->getLocation() === urldecode($location);
            });
        }

        return $filteredServers;
    }

    public function getLocationOptions(): array
    {
        $locations =  array_map(function ($server) {
            return $server->getLocation();
        }, $this->servers);
        
        return array_unique($locations);
    }
}
