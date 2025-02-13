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

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'reparation')]
    private Collection $produit;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'reparation')]
    private Collection $tickets;

    #[ORM\ManyToOne(inversedBy: 'reparation')]
    private ?RendezVous $rendezVous = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'attribuer')]
    private Collection $produits;

    public function __construct()
    {
        $this->produit = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->produits = new ArrayCollection();
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

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setReparation($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getReparation() === $this) {
                $produit->setReparation(null);
            }
        }

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
            // set the owning side to null (unless already changed)
            if ($ticket->getReparation() === $this) {
                $ticket->setReparation(null);
            }
        }

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
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }
    public function __toString(): string
    {
        return $this->diagnostic ?? 'Réparation #'.$this->id;
    }
}
