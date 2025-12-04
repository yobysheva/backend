<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\ApplicationController;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/create_application',
            controller: ApplicationController::class.'::createApplication',
        ),
        new Get(
            uriTemplate: '/applications/{id}',
            controller: ApplicationController::class.'::getApplicationById',
        ),
    ],
    extraProperties: [
        'openapi_context' => [
            'post' => [
                'summary' => 'Create application',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'user_id' => ['type' => 'integer'],
                                    'house_id' => ['type' => 'integer'],
                                ],
                                'required' => ['user_id', 'house_id'],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => ['description' => 'Application created'],
                    '400' => ['description' => 'Invalid input'],
                    '404' => ['description' => 'User or House not found'],
                ],
            ],
            'get' => [
                'summary' => 'Get application by ID',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'responses' => [
                    '200' => ['description' => 'Application details'],
                    '404' => ['description' => 'Application not found'],
                ],
            ],
        ],
    ]
)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $applicant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?House $wanted_house = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getWantedHouse(): ?House
    {
        return $this->wanted_house;
    }

    public function setWantedHouse(?House $wanted_house): static
    {
        $this->wanted_house = $wanted_house;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getApplicant() ?? (string) $this->getWantedHouse() ?? (string) $this->getId();
    }
}
