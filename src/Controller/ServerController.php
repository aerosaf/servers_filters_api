<?php

namespace App\Controller;

use App\Entity\Server;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServerController extends AbstractController
{
    private $servers = [];

    public function __construct(KernelInterface $kernel)
    {
        $projectDir = $kernel->getProjectDir();
        // Replace "path/to/excel/file.xlsx" with the actual path to your Excel file
        $absolutePath = $projectDir . '/public/LeaseWeb_servers_filters_assignment.xlsx';
        $spreadsheet = IOFactory::load($absolutePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        foreach ($worksheet->getRowIterator(2) as $row) {
            $server = new Server();
            $server->setModel($worksheet->getCell('A'.$row->getRowIndex())->getValue());
            $server->setRam($worksheet->getCell('B'.$row->getRowIndex())->getValue());
            $server->setHdd($worksheet->getCell('C'.$row->getRowIndex())->getValue());
            $server->setLocation($worksheet->getCell('D'.$row->getRowIndex())->getValue());
            $server->setPrice($worksheet->getCell('E'.$row->getRowIndex())->getValue());
            // populate $this->servers from Excel file
            $this->servers[] = $server;
        }
    }

    #[Route("/server/list", name:"server_list")]
    public function list(Request $request): JsonResponse
    {
        $filteredServers = $this->servers;

        // filter by storage
        $minStorage = $request->query->get('minStorage');
        $maxStorage = $request->query->get('maxStorage');
        if ($maxStorage && $maxStorage) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($minStorage, $maxStorage) {
                $serverStorage = $server->getStorage();
                return $serverStorage >= $minStorage && $serverStorage <= $maxStorage;
            });
        }

        // filter by RAM
        $ram = $request->query->get('ram');
        if ($ram) {
            $ramValues = explode(',', $ram);
            $filteredServers = array_filter($filteredServers, function ($server) use ($ramValues) {
                foreach ($ramValues as $values) {
                    if (preg_match('/^'.$values.'/', $server->getRam())) {
                        return $server;
                    }
                }
            });
        }

        // filter by harddisk type
        $harddiskType = $request->query->get('harddisk_type');
        if ($harddiskType) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($harddiskType) {
                return $server->getHarddiskType() === $harddiskType;
            });
        }

        // filter by location
        $location = $request->query->get('location');
        if ($location) {
            $filteredServers = array_filter($filteredServers, function ($server) use ($location) {
                return $server->getLocation() === $location;
            });
        }

        // return filtered products
        return $this->json([
            'data' => $filteredServers
        ]);
    }
}