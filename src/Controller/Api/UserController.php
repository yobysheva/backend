<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/api/create_user', name: 'api_create_user', methods: ['POST'])]
    public function createUser(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'], $data['phone'])) {
            return new JsonResponse(['error' => 'name and phone fields requered'], 400);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setPhone($data['phone']);
        
        $userRepository->save($user);

        return new JsonResponse([
            'status' => 'user created', 
            'id' => $user->getId()]
        , 201);
    }
    #[Route('/api/users/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'phone' => $user->getPhone(),
            'name' => $user->getName(),
        ]);
    }
}
