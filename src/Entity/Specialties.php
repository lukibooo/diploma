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

    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?int $price = null;

    #[ORM\ManyToOne(targetEntity: University::class, inversedBy: 'specialties')]
    private ?University $university = null;
    #[ORM\OneToMany(targetEntity: Number::class, mappedBy: 'specialty')]
    private Collection $numbers;

    public function __construct()
    {
        $this->numbers = new ArrayCollection();
    }

    public function getNumbers(): Collection
    {
        return $this->numbers;
    }
//    #[ORM\OneToMany(targetEntity: Subject::class, mappedBy: 'specialty', cascade: ['persist', 'remove'])]
//    private Collection $subjects;
//
//    public function __construct()
//    {
//        $this->subjects = new ArrayCollection();
//    }
//
//    public function getSubjects(): Collection
//    {
//        return $this->subjects;
//    }
//
//    public function addSubject(Subject $subject): static
//    {
//        if (!$this->subjects->contains($subject)) {
//            $this->subjects[] = $subject;
//            $subject->setNumber($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSubject(Subject $subject): static
//    {
//        if ($this->subjects->removeElement($subject)) {
//            // set the owning side to null
//            if ($subject->getNumber() === $this) {
//                $subject->setNumber(null);
//            }
//        }
//
//        return $this;
//    }
    public function getUniversity(): ?University
    {
        return $this->university;
    }

    public function setUniversity(?University $university): static
    {
        $this->university = $university;
        return $this;
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
    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

}
