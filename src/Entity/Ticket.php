<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $objetTicket = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionTicket = null;

    #[ORM\Column(length: 255)]
    private ?string $statusTicket = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreationTicket = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMajTicket = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Reparation $reparation = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjetTicket(): ?string
    {
        return $this->objetTicket;
    }

    public function setObjetTicket(string $objetTicket): static
    {
        $this->objetTicket = $objetTicket;

        return $this;
    }

    public function getDescriptionTicket(): ?string
    {
        return $this->descriptionTicket;
    }

    public function setDescriptionTicket(string $descriptionTicket): static
    {
        $this->descriptionTicket = $descriptionTicket;

        return $this;
    }

    public function getStatusTicket(): ?string
    {
        return $this->statusTicket;
    }

    public function setStatusTicket(string $statusTicket): static
    {
        $this->statusTicket = $statusTicket;

        return $this;
    }

    public function getDateCreationTicket(): ?\DateTimeInterface
    {
        return $this->dateCreationTicket;
    }

    public function setDateCreationTicket(\DateTimeInterface $dateCreationTicket): static
    {
        $this->dateCreationTicket = $dateCreationTicket;

        return $this;
    }

    public function getDateMajTicket(): ?\DateTimeInterface
    {
        return $this->dateMajTicket;
    }

    public function setDateMajTicket(?\DateTimeInterface $dateMajTicket): static
    {
        $this->dateMajTicket = $dateMajTicket;

        return $this;
    }

    public function getReparation(): ?Reparation
    {
        return $this->reparation;
    }

    public function setReparation(?Reparation $reparation): static
    {
        $this->reparation = $reparation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
