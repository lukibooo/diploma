<?php

namespace App\Entity;

use App\Repository\SpecialtiesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SpecialtiesRepository::class)]
class Specialties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[ORM\ManyToMany(targetEntity: Number::class, mappedBy: 'specialties')]
    private Collection $numbers;
    #[ORM\ManyToMany(targetEntity: University::class, inversedBy: 'specialties')]
    #[ORM\JoinTable(name: "specialty_university")]
    private Collection $universities;

    #[ORM\OneToMany(targetEntity: SpecialtyPrice::class, mappedBy: 'specialty')]
    private Collection $specialtyPrice;

    public function __construct()
    {
        $this->numbers = new ArrayCollection();
        $this->universities = new ArrayCollection();
        $this->specialtyPrice = new ArrayCollection();
    }

    public function getNumbers(): Collection
    {
        return $this->numbers;
    }


    public function addUniversity(University $university): self {
        if (!$this->universities->contains($university)) {
            $this->universities[] = $university;
        }
        return $this;
    }

    public function getUniversities(): Collection {
        return $this->universities;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getSpecialtyPrice(): Collection
    {
        return $this->specialtyPrice;
    }

}
