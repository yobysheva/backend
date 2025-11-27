<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class AuthenticatedApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;
    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        // createClient() сам boot'ит kernel
        $this->client = static::createClient();

        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);

        $user = new User();
        $user->setName('Test');
        $user->setPhone('test_'.uniqid());
        $user->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        $this->testUser = $user;

        $this->client->loginUser($user);
    }
}
