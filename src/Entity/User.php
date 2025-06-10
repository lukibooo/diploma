<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private array $roles = [];
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $about = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject1Name = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subject1Score = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject2Name = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subject2Score = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject3Name = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subject3Score = null;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject4Name = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subject4Score = null;

    #[ORM\ManyToMany(targetEntity: Interest::class, mappedBy: 'users')]
    private Collection $interests;

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Якщо не зберігаєш тимчасові чутливі дані — залиш порожнім
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;
        return $this;
    }
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->interests = new ArrayCollection();
    }
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
    public function getSubject1Name(): ?string
    {
        return $this->subject1Name;
    }

    public function setSubject1Name(?string $subject1Name): self
    {
        $this->subject1Name = $subject1Name;
        return $this;
    }

    public function getSubject1Score(): ?int
    {
        return $this->subject1Score;
    }

    public function setSubject1Score(?int $subject1Score): self
    {
        $this->subject1Score = $subject1Score;
        return $this;
    }

// Subject 2
    public function getSubject2Name(): ?string
    {
        return $this->subject2Name;
    }

    public function setSubject2Name(?string $subject2Name): self
    {
        $this->subject2Name = $subject2Name;
        return $this;
    }

    public function getSubject2Score(): ?int
    {
        return $this->subject2Score;
    }

    public function setSubject2Score(?int $subject2Score): self
    {
        $this->subject2Score = $subject2Score;
        return $this;
    }

// Subject 3
    public function getSubject3Name(): ?string
    {
        return $this->subject3Name;
    }

    public function setSubject3Name(?string $subject3Name): self
    {
        $this->subject3Name = $subject3Name;
        return $this;
    }

    public function getSubject3Score(): ?int
    {
        return $this->subject3Score;
    }

    public function setSubject3Score(?int $subject3Score): self
    {
        $this->subject3Score = $subject3Score;
        return $this;
    }

// Subject 4
    public function getSubject4Name(): ?string
    {
        return $this->subject4Name;
    }

    public function setSubject4Name(?string $subject4Name): self
    {
        $this->subject4Name = $subject4Name;
        return $this;
    }

    public function getSubject4Score(): ?int
    {
        return $this->subject4Score;
    }

    public function setSubject4Score(?int $subject4Score): self
    {
        $this->subject4Score = $subject4Score;
        return $this;
    }

    /**
     * @return Collection<int, Interest>
     */
    public function getInterests(): Collection
    {
        return $this->interests;
    }

    public function addInterest(Interest $interest): static
    {
        if (!$this->interests->contains($interest)) {
            $this->interests->add($interest);
            $interest->addUser($this);
        }

        return $this;
    }

    public function removeInterest(Interest $interest): static
    {
        if ($this->interests->removeElement($interest)) {
            $interest->removeUser($this);
        }

        return $this;
    }
}
