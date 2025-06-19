<?php

namespace App\Entity;

use App\Repository\SpecialtyPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialtyPriceRepository::class)]
class SpecialtyPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'specialtyPrice')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Specialties $specialty = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?University $university = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSpecialty(): ?Specialties
    {
        return $this->specialty;
    }

    public function setSpecialty(?Specialties $specialty): static
    {
        $this->specialty = $specialty;

        return $this;
    }

    public function getUniversity(): ?University
    {
        return $this->university;
    }

    public function setUniversity(?University $university): static
    {
        $this->university = $university;

        return $this;
    }
}
