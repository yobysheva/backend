<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\House;

class HouseControllerTest extends WebTestCase
{
    public function testCreateHouseSuccessfully(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $requestData = [
            'spaciousness' => 2,
            'line' => 3,
            'bathroom' => true,
            'shower' => false
        ];

        $client->request('POST', '/api/create_house', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($requestData));
        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $houseId = $responseData['id'];

        $house = $em->getRepository(House::class)->find($houseId);
        $this->assertNotNull($house);
        $this->assertSame($requestData['spaciousness'], $house->getSpaciousness());
        $this->assertSame($requestData['line'], $house->getLine());
        $this->assertSame($requestData['bathroom'], $house->isBathroom());
        $this->assertSame($requestData['shower'], $house->isShower());
    }

    public function testGetHouseByIdSuccessfully(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/create_house', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'spaciousness' => 4,
            'line' => 1,
            'bathroom' => true,
            'shower' => true
        ]));
        $response = json_decode($client->getResponse()->getContent(), true);
        $houseId = $response['id'];

        $client->request('GET', "/api/houses/$houseId");
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(4, $data['spaciousness']);
        $this->assertSame(1, $data['line']);
        $this->assertTrue($data['bathroom']);
        $this->assertTrue($data['shower']);
    }

    public function testCreateHouseMissingFields(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/create_house', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'spaciousness' => 3
        ]));

        $this->assertResponseStatusCodeSame(400);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreateHouseInvalidType(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/create_house', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'spaciousness' => 'big',
            'line' => 2
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetHouseNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/houses/999999');
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

}
