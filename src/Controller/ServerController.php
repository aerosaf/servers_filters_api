<?php
namespace App\Controller;

use App\Entity\ServerFilter;
use App\Service\ServerService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServerController extends AbstractController
{
    private $serverService;
    private $validator;

    public function __construct(ServerService $serverService, ValidatorInterface $validator)
    {
        $this->serverService = $serverService;
        $this->validator = $validator;
    }

    /**
     * @param RequestStack $requestStack
     * @return JsonResponse
     */
    #[Route("/server/list", name: "server_list")]
    public function listServers(RequestStack $requestStack): JsonResponse
    {
        $request = $requestStack->getCurrentRequest();

        $serverFilter = new ServerFilter(
            $request->get(ServerFilter::MIN_STORAGE) !== null ? (int)$request->get(ServerFilter::MIN_STORAGE) : null,
            $request->get(ServerFilter::MAX_STORAGE) !== null ? (int)$request->get(ServerFilter::MAX_STORAGE) : null,
            $request->get(ServerFilter::RAM) !== null ? (int)$request->get(ServerFilter::RAM) : null,
            $request->get(ServerFilter::HARD_DISK_TYPE),
            $request->get(ServerFilter::LOCATION)
        );

        $errors = $this->validator->validate($serverFilter);

        $filteredServers = $this->serverService->getServersExcel($serverFilter);

        if (count($errors) > 0) {
            return $this->json([
                'data' => [],
                'errors' => $errors
            ]);
        } else {
            return $this->json([
                'data' => array_values($filteredServers)
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    #[Route("/server/location", name: "server_location")]
    public function listServerLocations(): JsonResponse
    {
        $locations = $this->serverService->getLocationOptions();
        return $this->json([
            'data' => array_values($locations)
        ]);
    }
}