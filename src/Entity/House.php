<?php

namespace App\Entity;

use App\Repository\HouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseRepository::class)]
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
    private ?bool $bathroom = null;

    #[ORM\Column]
    private ?bool $shower = null;

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

    public function setUserForHouse(?User $user_for_house): static
    {
        // unset the owning side of the relation if necessary
        if ($user_for_house === null && $this->user_for_house !== null) {
            $this->user_for_house->setcurrentHouse(null);
        }

        // set the owning side of the relation if necessary
        if ($user_for_house !== null && $user_for_house->getcurrentHouse() !== $this) {
            $user_for_house->setcurrentHouse($this);
        }

        $this->user_for_house = $user_for_house;

        return $this;
    }
}
