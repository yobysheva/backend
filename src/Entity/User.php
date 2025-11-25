<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $role = 'guest';

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

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

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
}
