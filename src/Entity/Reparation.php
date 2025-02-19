<?php

namespace App\Entity;

use App\Repository\ReparationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReparationRepository::class)]
class Reparation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $diagnostic = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureReparation = null;

    #[ORM\Column(length: 255)]
    private ?string $statutReparation = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'reparations')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Produit $produit = null;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'reparation', cascade: ['persist', 'remove'])]
    private Collection $tickets;

    #[ORM\ManyToOne(inversedBy: 'reparations')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?RendezVous $rendezVous = null;
    #[ORM\PreUpdate]
    public function updateTicketStatut()
    {
        if ($this->getTickets()) {
            foreach ($this->getTickets() as $ticket) {
                if ($this->statutReparation === 'Terminé') {
                    $ticket->setStatutTicket('résolu');
                } else {
                    $ticket->setStatutTicket('en cours');
                }
            }
        }
    }
    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiagnostic(): ?string
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(string $diagnostic): static
    {
        $this->diagnostic = $diagnostic;
        return $this;
    }

    public function getDateHeureReparation(): ?\DateTimeInterface
    {
        return $this->dateHeureReparation;
    }

    public function setDateHeureReparation(\DateTimeInterface $dateHeureReparation): static
    {
        $this->dateHeureReparation = $dateHeureReparation;
        return $this;
    }

    public function getStatutReparation(): ?string
    {
        return $this->statutReparation;
    }

    public function setStatutReparation(string $statutReparation): static
    {
        $this->statutReparation = $statutReparation;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function getRendezVous(): ?RendezVous
    {
        return $this->rendezVous;
    }

    public function setRendezVous(?RendezVous $rendezVous): static
    {
        $this->rendezVous = $rendezVous;
        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setReparation($this);
        }
        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getReparation() === $this) {
                $ticket->setReparation(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return "Réparation: " . $this->diagnostic . " (" . $this->statutReparation . ")";
    }
}
