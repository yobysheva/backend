<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\UserController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/user/create_user',
            controller: UserController::class.'::createUser',
        ),
        new Get(
            uriTemplate: '/users/{id}',
            controller: UserController::class.'::getUserById',
        ),
    ],
    extraProperties: [
        'openapi_context' => [
            'post' => [
                'summary' => 'Create a new user',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'phone' => ['type' => 'string'],
                                    'password' => ['type' => 'string'],
                                    'role' => ['type' => 'string'],
                                ],
                                'required' => ['name', 'phone', 'password'],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => ['description' => 'User created'],
                    '400' => ['description' => 'Invalid input'],
                    '409' => ['description' => 'User already exists'],
                ],
            ],
            'get' => [
                'summary' => 'Get user by ID',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'responses' => [
                    '200' => ['description' => 'User details'],
                    '404' => ['description' => 'User not found'],
                ],
            ],
        ],
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(targetEntity: Application::class, mappedBy: 'applicant')]
    private Collection $applications;

    #[ORM\OneToOne(inversedBy: 'user_for_house', cascade: ['persist', 'remove'])]
    private ?House $currentHouse = null;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->password = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        if (null === $this->phone || '' === $this->phone) {
            throw new LogicException('Phone must be set');
        }

        // @psalm-return non-empty-string
        return $this->phone;
    }

    /**
     * @see UserInterface
     */
    #[Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;

        return array_unique(array_filter($roles, fn ($role) => is_string($role)));
    }

    #[Override]
    public function eraseCredentials(): void {}

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    #[Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application_el): static
    {
        if (!$this->applications->contains($application_el)) {
            $this->applications->add($application_el);
            $application_el->setApplicant($this);
        }

        return $this;
    }

    public function removeApplication(Application $application_el): static
    {
        if ($this->applications->removeElement($application_el)) {
            // set the owning side to null (unless already changed)
            if ($application_el->getApplicant() === $this) {
                $application_el->setApplicant(null);
            }
        }

        return $this;
    }

    public function getcurrentHouse(): ?House
    {
        return $this->currentHouse;
    }

    public function setcurrentHouse(?House $currentHouse): static
    {
        $this->currentHouse = $currentHouse;

        return $this;
    }

    public function __toString(): string
    {
        $rolesString = implode(', ', $this->getRoles());

        return (string) $this->getPhone() ?? (string) $this->getName() ?? $rolesString ?? (string) $this->getId();
    }
}
