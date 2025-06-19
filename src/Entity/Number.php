<?php

namespace App\Entity;

use App\Repository\NumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NumberRepository::class)]
class Number
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Specialties::class, inversedBy: "numbers")]
    private Collection $specialties;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: "numbers")]
    private Collection $subjects;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->specialties = new ArrayCollection();
    }
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
        }

        return $this;
    }


    public function getSpecialties(): Collection
    {
        return $this->specialties;
    }

    public function addSpecialty(Specialties $specialty): self
    {
        if (!$this->specialties->contains($specialty)) {
            $this->specialties[] = $specialty;
        }

        return $this;
    }

    public function removeSpecialty(Specialties $specialty): self
    {
        $this->specialties->removeElement($specialty);
        return $this;
    }
    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): static
    {
        $this->value = $value;
        return $this;
    }

}
