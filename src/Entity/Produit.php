<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Image;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelleProduit = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixUnitaire = null;

    #[ORM\Column(length: 255)]
    private ?string $typeProduit = null;

    #[ORM\Column(nullable: true)]
    private ?int $qteStock = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'produit', orphanRemoval: true)]
    private Collection $image;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true,  onDelete: 'SET NULL')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Reparation::class, cascade: ['persist', 'remove'])]
    private Collection $reparations;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->reparations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleProduit(): ?string
    {
        return $this->libelleProduit;
    }

    public function setLibelleProduit(string $libelleProduit): static
    {
        $this->libelleProduit = $libelleProduit;
        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(?float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getTypeProduit(): ?string
    {
        return $this->typeProduit;
    }

    public function setTypeProduit(string $typeProduit): static
    {
        $this->typeProduit = $typeProduit;
        return $this;
    }

    public function getQteStock(): ?int
    {
        return $this->qteStock;
    }

    public function setQteStock(?int $qteStock): static
    {
        $this->qteStock = $qteStock;
        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setProduit($this);
        }
        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->image->removeElement($image)) {
            if ($image->getProduit() === $this) {
                $image->setProduit(null);
            }
        }
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
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
    public function getReparations(): Collection
    {
        return $this->reparations;
    }

    public function addReparation(Reparation $reparation): static
    {
        if (!$this->reparations->contains($reparation)) {
            $this->reparations->add($reparation);
            $reparation->setProduit($this);
        }
        return $this;
    }

    public function removeReparation(Reparation $reparation): static
    {
        if ($this->reparations->removeElement($reparation)) {
            if ($reparation->getProduit() === $this) {
                $reparation->setProduit(null);
            }
        }
        return $this;
    }
    public function getFormattedStock(): string
    {
        if ($this->qteStock === null) {
            return '<span style="color: gray; font-weight: bold;">Stock inconnu</span>';
        }
        if ($this->qteStock == 0) {
            return '<span style="color: red; font-weight: bold;">Rupture de stock</span>';
        }
        if ($this->qteStock <= 5) {
            return '<span style="color: orange; font-weight: bold;">Stock faible (' . $this->qteStock . ')</span>';
        }
        return '<span style="color: green;">' . $this->qteStock . '</span>';
    }
    
    public function __toString(): string
    {
        return $this->libelleProduit ?? 'Produit #'.$this->id;
    }
}
