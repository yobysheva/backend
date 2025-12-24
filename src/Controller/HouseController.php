<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\HouseServiceCSV;

final class HouseController extends AbstractController
{
    private HouseServiceCSV $csvService;

    public function __construct(HouseServiceCSV $csvService) {
    $this->csvService = $csvService;
    }


    #[Route('/', name: 'houses', methods:['GET'])]
    public function listHouses(): Response
    {
        $houses = $this->csvService->getAllHouses();
        return $this->json($houses);
    }

}
