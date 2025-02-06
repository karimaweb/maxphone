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

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?Utilisateur $utilisateur = null;

    /**
     * @var Collection<int, Reparation>
     */
    #[ORM\OneToMany(targetEntity: Reparation::class, mappedBy: 'rendezVous')]
    private Collection $reparation;

    public function __construct()
    {
        $this->reparation = new ArrayCollection();
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, Reparation>
     */
    public function getReparation(): Collection
    {
        return $this->reparation;
    }

    public function addReparation(Reparation $reparation): static
    {
        if (!$this->reparation->contains($reparation)) {
            $this->reparation->add($reparation);
            $reparation->setRendezVous($this);
        }

        return $this;
    }

    public function removeReparation(Reparation $reparation): static
    {
        if ($this->reparation->removeElement($reparation)) {
            // set the owning side to null (unless already changed)
            if ($reparation->getRendezVous() === $this) {
                $reparation->setRendezVous(null);
            }
        }

        return $this;
    }
}
