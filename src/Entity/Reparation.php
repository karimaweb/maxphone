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

    #[ORM\OneToMany(mappedBy: 'reparation', targetEntity: Ticket::class, cascade: ['persist', 'remove'])]
    private Collection $tickets;
    #[ORM\OneToMany(targetEntity: HistoriqueReparation::class, mappedBy: "reparation", cascade: ["persist", "remove"])]
    private Collection $historiques;

    #[ORM\ManyToOne(inversedBy: 'reparations')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')] //  Permet d'avoir un NULL pour les réparations sans rdv
    private ?RendezVous $rendezVous = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'reparations')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')] 
    private ?Utilisateur $utilisateur = null;
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
        $this->historiques = new ArrayCollection();
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
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
    public function getClientNom(): ?string
    {
    if ($this->rendezVous && $this->rendezVous->getUtilisateur()) {
        return $this->rendezVous->getUtilisateur()->getNomUtilisateur() . ' ' .
               $this->rendezVous->getUtilisateur()->getPrenomUtilisateur();
    }

    if ($this->tickets->count() > 0) {
        $ticket = $this->tickets->first();
        if ($ticket && $ticket->getUtilisateur()) {
            return $ticket->getUtilisateur()->getNomUtilisateur() . ' ' .
                   $ticket->getUtilisateur()->getPrenomUtilisateur();
        }
    }

    return 'Aucun client';
    }
    public function getFormattedStatut(): string
    {
        return match ($this->statutReparation) {
            'en attente' => '<span class="badge bg-warning">En attente</span>',
            'en cours' => '<span class="badge bg-primary">En cours</span>',
            'terminé' => '<span class="badge bg-success">Terminé</span>',
            default => '<span class="badge bg-secondary">Inconnu</span>',
        };


        return $badges[$this->statutReparation] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }
    public function getFormattedRendezVous(): string
    {
        return $this->rendezVous ? $this->rendezVous->getDateHeureRendezVous()->format('d/m/Y H:i') . ' - confirmé' 
        : '<span style="color: red; font-weight: bold;">Sans RDV</span>';
    }

public function getFormattedClient(): string
{
    if ($this->rendezVous && $this->rendezVous->getUtilisateur()) {
        return $this->rendezVous->getUtilisateur()->getNomUtilisateur();
    } elseif ($this->utilisateur) {
        return $this->utilisateur->getNomUtilisateur();
    }

    return '<span style="color: orange; font-weight: bold;">Aucun client</span>';
    }
    
    public function __toString(): string
    {
            return "Réparation: " . $this->diagnostic . " (" . $this->statutReparation . ")";
    }
//     public function addHistorique(HistoriqueReparation $historique): self
// {
//     if (!$this->historiques->contains($historique)) {
//         $this->historiques[] = $historique;
//         $historique->setReparation($this);
//     }
//     return $this;
public function logHistorique(PreUpdateEventArgs $event): void
{
    if ($event->hasChangedField('statutReparation')) { // ✅ Vérifie si le statut a changé
        $historique = new HistoriqueReparation();
        $historique->setReparation($this);
        $historique->setStatutHistoriqueReparation($this->getStatutReparation()); // ✅ Correction ici
        $historique->setCommentaire('Mise à jour automatique.');
        $historique->setDateMajReparation(new \DateTime());

        $event->getObjectManager()->persist($historique);
        $event->getObjectManager()->flush();
    }
    }
    public function getHistoriques(): Collection
{
    return $this->historiques;
}
}

