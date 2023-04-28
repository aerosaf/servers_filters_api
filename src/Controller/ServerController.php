<?php

namespace App\Controller;

use App\Entity\ServerFilter;
use App\Service\ServerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServerController extends AbstractController
{
    private $serverService;

    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    #[Route("/server/list", name: "server_list")]
    public function list(Request $request): JsonResponse
    {
        $filter = new ServerFilter(
            $request->query->get('minStorage'),
            $request->query->get('maxStorage'),
            $request->query->get('ram'),
            $request->query->get('harddisk_type'),
            $request->query->get('location')
        );
        
        $filteredServers = $this->serverService->getServersExcel($filter);

        // return filtered products
        return $this->json([
            'data' => array_values($filteredServers)
        ]);
    }

    #[Route("/server/location", name: "server_location")]
    public function location(): JsonResponse
    {
        $locations = $this->serverService->getLocationOptions();
        return $this->json([
            'data' => array_values($locations)
        ]);
    }
}
