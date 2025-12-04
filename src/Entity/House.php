<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\HouseController;
use App\Repository\HouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/create_house',
            controller: HouseController::class.'::createHouse',
        ),
        new Get(
            uriTemplate: '/houses/{id}',
            controller: HouseController::class.'::getHouseById',
        ),
    ],
    extraProperties: [
        'openapi_context' => [
            'post' => [
                'summary' => 'Create a new house',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'spaciousness' => ['type' => 'integer'],
                                    'line' => ['type' => 'integer'],
                                    'bathroom' => ['type' => 'boolean'],
                                    'shower' => ['type' => 'boolean'],
                                ],
                                'required' => ['spaciousness', 'line'],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => ['description' => 'House created successfully, returns ID'],
                    '400' => ['description' => 'Invalid input: fields missing or wrong type'],
                ],
            ],
            'get' => [
                'summary' => 'Get house by ID',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'House details',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                        'spaciousness' => ['type' => 'integer'],
                                        'line' => ['type' => 'integer'],
                                        'bathroom' => ['type' => 'boolean'],
                                        'shower' => ['type' => 'boolean'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '404' => ['description' => 'House not found'],
                ],
            ],
        ],
    ]
)]
class House
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $spaciousness = null;

    #[ORM\Column]
    private ?int $line = null;

    #[ORM\Column]
    private ?bool $bathroom = false;

    #[ORM\Column]
    private ?bool $shower = false;

    #[ORM\OneToOne(mappedBy: 'currentHouse', cascade: ['persist', 'remove'])]
    private ?User $user_for_house = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpaciousness(): ?int
    {
        return $this->spaciousness;
    }

    public function setSpaciousness(int $spaciousness): static
    {
        $this->spaciousness = $spaciousness;

        return $this;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function setLine(int $line): static
    {
        $this->line = $line;

        return $this;
    }

    public function isBathroom(): ?bool
    {
        return $this->bathroom;
    }

    public function setBathroom(bool $bathroom): static
    {
        $this->bathroom = $bathroom;

        return $this;
    }

    public function isShower(): ?bool
    {
        return $this->shower;
    }

    public function setShower(bool $shower): static
    {
        $this->shower = $shower;

        return $this;
    }

    public function getUserForHouse(): ?User
    {
        return $this->user_for_house;
    }

    public function __toString(): string
    {
        return (string) $this->getSpaciousness() ?? (string) $this->getLine() ?? (string) $this->getId();
    }

    public function setUserForHouse(?User $user_for_house): static
    {
        // unset the owning side of the relation if necessary
        if (null === $user_for_house && null !== $this->user_for_house) {
            $this->user_for_house->setcurrentHouse(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $user_for_house && $user_for_house->getcurrentHouse() !== $this) {
            $user_for_house->setcurrentHouse($this);
        }

        $this->user_for_house = $user_for_house;

        return $this;
    }
}
