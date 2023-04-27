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
        return $this->json([
            'data' => $this->servers
        ]);
    }
}