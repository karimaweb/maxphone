<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureRendezVous = null;

    #[ORM\Column(length: 255)]
    private ?string $statutRendezVous = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;
    /**
     * @var Collection<int, Reparation>
     */
    #[ORM\OneToMany(targetEntity: Reparation::class, mappedBy: 'rendezVous')]
    private Collection $reparations;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->reparations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeureRendezVous(): ?\DateTimeInterface
    {
        return $this->dateHeureRendezVous;
    }

    public function setDateHeureRendezVous(\DateTimeInterface $dateHeureRendezVous): static
    {
        $this->dateHeureRendezVous = $dateHeureRendezVous;

        return $this;
    }

    public function getStatutRendezVous(): ?string
    {
        return $this->statutRendezVous;
    }

    public function setStatutRendezVous(string $statutRendezVous): static
    {
        $this->statutRendezVous = $statutRendezVous;

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

    

    /**
     * @return Collection<int, Reparation>
     */
    public function getReparations(): Collection
    {
        return $this->reparations;
    }

    public function addReparation(Reparation $reparation): static
    {
        if (!$this->reparations->contains($reparation)) {
            $this->reparations->add($reparation);
            $reparation->setRendezVous($this);
        }

        return $this;
    }

    public function removeReparation(Reparation $reparation): static
    {
        if ($this->reparations->removeElement($reparation)) {
            // set the owning side to null (unless already changed)
            if ($reparation->getRendezVous() === $this) {
                $reparation->setRendezVous(null);
            }
        }

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
    public function getFormattedStatut(): string
    {
    $badges = [
        'en attente' => '<span class="badge bg-warning">En attente</span>',
        'confirmé' => '<span class="badge bg-success">Confirmé</span>',
        'annulé' => '<span class="badge bg-danger">Annulé</span>',
    ];

    return $badges[$this->statutRendezVous] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }
    public function getFormattedDate(): string
{
    $now = new \DateTime();
    $diff = $now->diff($this->dateHeureRendezVous);

    if ($diff->invert === 1) { // RDV passé
        return '<span style="color: dark; font-weight: bold;">Passé: ' . $this->dateHeureRendezVous->format('d/m/Y H:i') . '</span>';
    }

    if ($diff->days === 0) { // RDV dans moins de 24h
        return '<span style="color: red; font-weight: bold;">Bientôt: ' . $this->dateHeureRendezVous->format('d/m/Y H:i') . '</span>';
    }

    if ($diff->days <= 7) { // RDV dans la semaine
        return '<span style="color: orange; font-weight: bold;">Bientôt: ' . $this->dateHeureRendezVous->format('d/m/Y H:i') . '</span>';
    }

    return '<span style="color: green;">' . $this->dateHeureRendezVous->format('d/m/Y H:i') . '</span>';
}


    
    
    public function __toString(): string
    {
    return $this->getDateHeureRendezVous()->format('d/m/Y H:i') . ' - ' . $this->getStatutRendezVous();
    }

}
