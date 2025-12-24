<?php

namespace App\Controller\Api;

use App\Entity\Application;
use App\Entity\User;
use App\Entity\House;
use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApplicationController extends AbstractController
{
    #[Route('/api/create_application', name: 'api_create_application', methods: ['POST'])]
    public function createApplication(
        Request $request, 
        UserRepository $userRepository,
        HouseRepository $houseRepository,
        ApplicationRepository $applicationRepository
        ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['user_id'], $data['house_id'])) {
            return new JsonResponse(['error' => 'user_id and house_id fields requered'], 400);
        }

        if (!is_int($data['user_id']) || !is_int($data['house_id'])) {
            return new JsonResponse(['error' => 'person_id and house_id must be integers'], 400);
        }

        $user = $userRepository->find($data['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $house = $houseRepository->find($data['house_id']);
        if (!$house) {
            return new JsonResponse(['error' => 'House not found'], 404);
        }

        $application = $applicationRepository->createForUserAndHouse($user, $house);

        return new JsonResponse([
            'status' => 'application created',
            'application_id' => $application->getId(),
            'user_id' => $user->getId(),
            'house_id' => $house->getId()
        ], 201);
    }

    #[Route('/api/applications/{id}', name: 'get_application_by_id', methods: ['GET'])]
    public function getApplicationById(int $id, ApplicationRepository $applicationRepository): JsonResponse
    {
        $application = $applicationRepository->find($id);

        if (!$application) {
            return new JsonResponse(['error' => 'Application not found'], 404);
        }

        return new JsonResponse([
            'id' => $application->getId(),
            'user_id' => $application->getApplicant()?->getId(),
            'house_id' => $application->getWantedHouse()?->getId(),
        ]);
    }
}
