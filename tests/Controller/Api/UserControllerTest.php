<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Repository\UserRepository;

class UserControllerTest extends WebTestCase
{
    public function testCreateUserSuccessfully(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $client->request('POST', '/api/create_user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Alice',
            'phone' => '1234567890'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->find($response['id']);

        $this->assertNotNull($user);
        $this->assertSame('Alice', $user->getName());
        $this->assertSame('1234567890', $user->getPhone());
    }

    public function testCreateUserMissingFields(): void
    {
    $client = static::createClient();
    $client->request('POST', '/api/create_user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
        'phone' => '123456'
    ]));

    $this->assertResponseStatusCodeSame(400);
    $data = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHasKey('error', $data);
    }  

    public function testGetUserByIdSuccessfully(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = new User();
        $user->setName('Bob');
        $user->setPhone('0987654321');
        
        $userRepository->save($user);
        $userId = $user->getId();

        $client->request('GET', "/api/users/$userId");
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Bob', $data['name']);
        $this->assertSame('0987654321', $data['phone']);
    }
}
