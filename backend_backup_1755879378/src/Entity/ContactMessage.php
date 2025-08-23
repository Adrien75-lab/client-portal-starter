<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ContactMessage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private string $subject = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $message = '';

    #[ORM\Column(nullable: true)]
    private ?string $attachmentFilename = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    // getters/setters omitted for brevity
}
