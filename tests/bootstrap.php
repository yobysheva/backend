<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\Tools\SchemaTool;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.test');
}

$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

$tool = new SchemaTool($entityManager);
$tool->dropSchema($metadata);
$tool->createSchema($metadata);