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

    #[ORM\ManyToOne(targetEntity: Specialties::class, inversedBy: 'numbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Specialties $specialty = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'numbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $value = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
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
