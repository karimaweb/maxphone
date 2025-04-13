<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]

class ActivationCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 6)]
    private ?string $code = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $expiresAt = null;

    public function __construct()
    {
        $this->expiresAt = (new \DateTime())->modify('+1 hour'); // Expire en 1h
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
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

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }
    public function setExpiresAt(): ?\DateTime
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }
    public function generateCode(): string
    {
        return (string) random_int(100000, 999999); // Génère un code à 6 chiffres
    }
}
