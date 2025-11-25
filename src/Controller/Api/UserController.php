<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name:'api')]
final class UserController extends AbstractController
{
    #[Route('/create_user', name: 'api_create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
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

        $plaintextPassword = $data['password'];

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        if (isset($data['role']) && is_string($data['role'])) {
            $user->setRoles($data['role']);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse(
            [
                'status' => 'user created',
                'id' => $user->getId()],
            201
        );
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
