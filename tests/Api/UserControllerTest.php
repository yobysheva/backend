<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\User;

/**
 * @internal
 *
 * @coversNothing
 */
class UserControllerTest extends AuthenticatedApiTestCase
{
    public function testCreateUserSuccessfully(): void
    {
        $this->client->request(
            'POST',
            '/api/user/create_user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Alice',
                'phone' => '1234567890',
                'password' => 'secret123',
                'roles' => ['ROLE_USER'],
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);

        $user = $this->em->getRepository(User::class)->find($response['id']);
        $this->assertNotNull($user);
        $this->assertSame('Alice', $user->getName());
        $this->assertSame('1234567890', $user->getPhone());
        $this->assertSame(['ROLE_USER'], $user->getRoles());

        $this->assertNotSame('secret123', $user->getPassword());
        $this->assertNotEmpty($user->getPassword());
    }

    public function testCreateUserMissingFields(): void
    {
        $this->client->request(
            'POST',
            '/api/user/create_user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => '123456',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetUserByIdSuccessfully(): void
    {
        $user = new User();
        $user->setName('Bob');
        $user->setPhone('0987654321');
        $user->setPassword('hashedpass');
        $user->setRoles(['guest']);

        $this->em->persist($user);
        $this->em->flush();
        $userId = $user->getId();

        $this->client->request('GET', "/api/users/{$userId}");
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Bob', $data['name']);
        $this->assertSame('0987654321', $data['phone']);
        $this->assertSame(['guest', 'ROLE_USER'], $data['role']);
    }
}
