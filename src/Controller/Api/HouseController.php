<?php

namespace App\Controller\Api;

use App\Entity\House;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HouseController extends AbstractController
{
    #[Route('/api/create_house', name: 'api_create_house', methods: ['POST'])]
    public function createHouse(Request $request, HouseRepository $houseRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !$data ||
            !isset($data['spaciousness'], $data['line']) ||
            !is_int($data['spaciousness']) ||
            !is_int($data['line'])
        ) {
            return new JsonResponse(['error' => 'Invalid fields: spaciousness and line must be integers'], 400);
        }

        $bathroom = $data['bathroom'] ?? false;
        $shower = $data['shower'] ?? false;

        if (!is_bool($bathroom) || !is_bool($shower)) {
            return new JsonResponse(['error' => 'bathroom and shower must be boolean'], 400);
        }

        $house = (new House())
            ->setSpaciousness($data['spaciousness'])
            ->setLine($data['line'])
            ->setBathroom($bathroom)
            ->setShower($shower);

        $houseRepository->save($house);

        return new JsonResponse(['id' => $house->getId()], 201);
    }

    #[Route('/api/houses/{id}', name: 'get_house_by_id', methods: ['GET'])]
    public function getHouseById(int $id, HouseRepository $houseRepository): JsonResponse
    {
        $house = $houseRepository->find($id);

        if (!$house) {
            return new JsonResponse(['error' => 'House not found'], 404);
        }

        return new JsonResponse([
            'id' => $house->getId(),
            'spaciousness' => $house->getSpaciousness(),
            'line' => $house->getLine(),
            'shower'=> $house->isShower(),
            'bathroom'=> $house->isBathroom(),
        ]);
    }
}
