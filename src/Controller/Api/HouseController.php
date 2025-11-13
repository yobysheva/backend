<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\House;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class HouseController extends AbstractController
{
    #[Route('/api/create_house', name: 'api_create_house', methods: ['POST'])]
    public function createHouse(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !$data
            || !isset($data['spaciousness'], $data['line'])
            || !is_int($data['spaciousness'])
            || !is_int($data['line'])
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
            ->setShower($shower)
        ;

        $em->persist($house);
        $em->flush();

        return new JsonResponse(['id' => $house->getId()], 201);
    }

    #[Route('/api/houses/{id}', name: 'get_house_by_id', methods: ['GET'])]
    public function getHouseById(int $id, EntityManagerInterface $em): JsonResponse
    {
        $house = $em->getRepository(House::class)->find($id);

        if (!$house) {
            return new JsonResponse(['error' => 'House not found'], 404);
        }

        return new JsonResponse([
            'id' => $house->getId(),
            'spaciousness' => $house->getSpaciousness(),
            'line' => $house->getLine(),
            'shower' => $house->isShower(),
            'bathroom' => $house->isBathroom(),
        ]);
    }
}
