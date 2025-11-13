<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\Application;
use App\Entity\House;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ApplicationControllerTest extends WebTestCase
{
    public function testCreateApplicationSuccessfully(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        $userData = ['name' => 'Charlie', 'phone' => '5555555555'];
        $client->request(
            'POST',
            '/api/create_user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );
        $responseUser = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseUser);
        $userId = $responseUser['id'];

        $houseData = ['spaciousness' => 8, 'line' => 2, 'bathroom' => false, 'shower' => true];
        $client->request(
            'POST',
            '/api/create_house',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($houseData)
        );
        $responseHouse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseHouse);
        $houseId = $responseHouse['id'];

        $requestData = ['user_id' => $userId, 'house_id' => $houseId];
        $client->request(
            'POST',
            '/api/create_application',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );
        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('application_id', $responseData);
        $applicationId = $responseData['application_id'];

        $em->clear();

        $user = $em->getRepository(User::class)->find($userId);
        $house = $em->getRepository(House::class)->find($houseId);
        $application = $em->getRepository(Application::class)->find($applicationId);

        $this->assertNotNull($application, 'Application should exist in DB');
        $this->assertNotNull($application->getApplicant(), 'Application applicant should not be null');
        $this->assertNotNull($application->getWantedHouse(), 'Application wanted house should not be null');

        $this->assertSame($user->getId(), $application->getApplicant()->getId());
        $this->assertSame($house->getId(), $application->getWantedHouse()->getId());
        $this->assertSame($house->getId(), $user->getCurrentHouse()->getId());
    }

    public function testGetApplicationByIdSuccessfully(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/create_user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Diana',
                'phone' => '6666666666',
            ])
        );
        $userId = json_decode($client->getResponse()->getContent(), true)['id'];

        $client->request(
            'POST',
            '/api/create_house',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'spaciousness' => 2,
                'line' => 4,
                'bathroom' => true,
                'shower' => false,
            ])
        );
        $houseId = json_decode($client->getResponse()->getContent(), true)['id'];

        $client->request(
            'POST',
            '/api/create_application',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => $userId,
                'house_id' => $houseId,
            ])
        );
        $applicationId = json_decode($client->getResponse()->getContent(), true)['application_id'];

        $client->request('GET', "/api/applications/{$applicationId}");
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($userId, $data['user_id']);
        $this->assertSame($houseId, $data['house_id']);
    }

    public function testCreateApplicationMissingFields(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/create_application',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => 1,
            ])
        );
        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreateApplicationUserNotFound(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/create_application',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => 999999999,
                'house_id' => 1,
            ])
        );

        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreateApplicationHouseNotFound(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/create_user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Eve',
                'phone' => '111222333',
            ])
        );
        $userId = json_decode($client->getResponse()->getContent(), true)['id'];

        $client->request(
            'POST',
            '/api/create_application',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'user_id' => $userId,
                'house_id' => 999999999,
            ])
        );

        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetApplicationNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/applications/9999999');
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
