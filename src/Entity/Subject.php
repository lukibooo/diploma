<?php

namespace App\Entity;

use App\Repository\SubjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectsRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $name = null;
    #[ORM\ManyToMany(targetEntity: Number::class, mappedBy: 'subjects')]
    private Collection $numbers;

    public function __construct()
    {
        $this->numbers = new ArrayCollection();
    }

    public function getNumbers(): Collection
    {
        return $this->numbers;
    }
//    #[ORM\ManyToOne(targetEntity: Number::class)]
//    #[ORM\JoinColumn(nullable: false)]
//    private ?Number $number = null;
//    #[ORM\Column(type: 'float')]
//    private ?float $coefficient = null;
//
//    public function getCoefficient(): ?float
//    {
//        return $this->coefficient;
//    }
//
//    public function setCoefficient(float $coefficient): static
//    {
//        $this->coefficient = $coefficient;
//        return $this;
//    }
    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

//    public function getNumber(): ?Number {
//        return $this->number;
//    }
//
//    public function setNumber(?Number $number): static {
//        $this->number = $number;
//        return $this;
//    }

}
