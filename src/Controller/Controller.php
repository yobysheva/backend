<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ServicesCSV;

final class Controller extends AbstractController
{
    private ServicesCSV $csvService;

    public function __construct(ServicesCSV $csvService) {
    $this->csvService = $csvService;
    }

    #[Route('/', name: 'houses')]
    public function listHouses(): Response
    {
        $houses = $this->csvService->getAllHouses();

        // Например, возвращаем JSON
        return $this->json($houses);
    }

    #[Route('/book', name:'book', methods:['POST'])]
    public function book(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $csvService->addBooking(
            $data['houseId'] ?? null,
            $data['phone'] ?? null,
            $data['comment'] ?? null
        );

        return $this->json(['status' => 'ok']);
    }

}
