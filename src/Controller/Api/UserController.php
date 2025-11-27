<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name:'api')]
final class UserController extends AbstractController
{
    #[Route(
        '/user/create_user',
        name: 'api_create_user',
        methods: ['POST']
    )]
    public function createUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'], $data['phone'], $data['password'])) {
            return new JsonResponse(['error' => 'name, phone and password fields requered'], 400);
        }

        assert(is_string($data['name']));
        assert(is_string($data['phone']));

        $name = $data['name'];

        $phone = $data['phone'];

        $user = new User();
        $user->setName($name);
        $user->setPhone($phone);

        $plaintextPassword = (string) $data['password'];

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        if (isset($data['role']) && is_string($data['role'])) {
            $user->setRoles([$data['role']]);
        }

        try {
            $em->persist($user);
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse(['error' => 'User with this phone already exists'], 409);
        }

        return new JsonResponse([
            'status' => 'user created',
            'id' => $user->getId(),
        ], 201);
    }

    #[Route('/users/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(int $id, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'phone' => $user->getPhone(),
            'name' => $user->getName(),
            'role' => $user->getRoles(),
        ]);
    }
}
