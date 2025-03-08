<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\HistoriqueReparationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: HistoriqueReparationRepository::class)]

class HistoriqueReparation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Reparation::class, inversedBy: "historiques")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Reparation $reparation = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?string $statutHistoriqueReparation = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $dateMajReparation = null;

    public function __construct()
    {
        $this->dateMajReparation = new \DateTime(); // ✅ Correction ici
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReparation(): ?Reparation
    {
        return $this->reparation;
    }

    public function setReparation(?Reparation $reparation): self
    {
        $this->reparation = $reparation;
        return $this;
    }

    public function getStatutHistoriqueReparation(): ?string
    {
        return $this->statutHistoriqueReparation;
    }

    public function setStatutHistoriqueReparation(string $statut): self
    {
        $this->statutHistoriqueReparation = $statut;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDateMajReparation(): ?\DateTimeInterface
    {
        return $this->dateMajReparation;
    }

    public function setDateMajReparation(\DateTimeInterface $date): self
    {
        $this->dateMajReparation = $date;
        return $this;
    }
}
