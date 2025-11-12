<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ServicesCSV;

final class Controller extends AbstractController
{
    private ServicesCSV $csvService;

    public function __construct(ServicesCSV $csvService)
    {
        $this->csvService = $csvService;
    }


    #[Route('/', name: 'houses', methods:['GET'])]
    public function listHouses(): Response
    {
        $houses = $this->csvService->getAllHouses();
        return $this->json($houses);
    }


    #[Route('/book', name:'book', methods:['POST'])]
    public function book(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $bookingId = $this->csvService->addBooking(
            $data['houseId'] ?? null,
            $data['phone'] ?? null,
            $data['comment'] ?? null
        );

        return $this->json(['status' => 'ok', 'booking_id' => $bookingId]);
    }


    #[Route('/changeBooking', name: 'changeBooking', methods:['PUT'])]
    public function changeBookingById(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $this->csvService->editBookingCommentById(
            $data['houseId'] ?? null,
            $data['newComment'] ?? null
        );

        return $this->json(['status' => 'ok']);
    }
}
