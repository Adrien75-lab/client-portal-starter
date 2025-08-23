<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email = '';

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ContactMessage::class, orphanRemoval: true)]
    private Collection $contactMessages;

    public function __construct()
    {
        $this->contactMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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
    public function eraseCredentials(): void {}
    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }
    public function setLastLoginAt(?\DateTimeImmutable $d): self
    {
        $this->lastLoginAt = $d;
        return $this;
    }

    /**
     * @return Collection<int, ContactMessage>
     */
    public function getContactMessages(): Collection
    {
        return $this->contactMessages;
    }

    public function addContactMessage(ContactMessage $message): self
    {
        if (!$this->contactMessages->contains($message)) {
            $this->contactMessages->add($message);
            $message->setUser($this);
        }
        return $this;
    }

    public function removeContactMessage(ContactMessage $message): self
    {
        if ($this->contactMessages->removeElement($message)) {
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }
        return $this;
    }

    #[Groups(['user:read'])]
    public function getFilesCount(): int
    {
        return $this->contactMessages->count();
    }
}
