<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $Nom_utilisateur = null;

    #[ORM\Column(length: 100)]
    private ?string $Prenom_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 100)]
    private ?string $Num_telephone = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'utilisateur')]
    private Collection $produits;
    /**
     * @var Collection<int, Reparation>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reparation::class, orphanRemoval: true)]
    private Collection $reparations;
    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'utilisateur')]
    private Collection $tickets;

    /**
     * @var Collection<int, RendezVous>
     */
    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'utilisateur')]
    private Collection $rendez_vous;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->rendez_vous = new ArrayCollection();
        $this->reparations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array

    {
        if ($this->roles === null) {
        $this->roles = []; // Évite les erreurs si la colonne `roles` est null
        }

        if (!in_array('ROLE_USER', $this->roles, true)) {
        $this->roles[] = 'ROLE_USER'; // Ajoute `ROLE_USER` si absent
        }

        return array_unique($this->roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static

    {
        if (!in_array('ROLE_USER', $roles, true)) {
        $roles[] = 'ROLE_USER'; // Toujours ajouter ROLE_USER
        }

        $this->roles = array_unique($roles);
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->Nom_utilisateur;
    }

    public function setNomUtilisateur(string $Nom_utilisateur): static
    {
        $this->Nom_utilisateur = $Nom_utilisateur;

        return $this;
    }

    public function getPrenomUtilisateur(): ?string
    {
        return $this->Prenom_utilisateur;
    }

    public function setPrenomUtilisateur(string $Prenom_utilisateur): static
    {
        $this->Prenom_utilisateur = $Prenom_utilisateur;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): static
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getNumTelephone(): ?string
    {
        return $this->Num_telephone;
    }

    public function setNumTelephone(string $Num_telephone): static
    {
        $this->Num_telephone = $Num_telephone;

        return $this;
    }

        /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits; 
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) { 
            $this->produits->add($produit);
            $produit->setUtilisateur($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) { 
            // set the owning side to null (unless already changed)
            if ($produit->getUtilisateur() === $this) {
                $produit->setUtilisateur(null);
            }
        }

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
            $reparation->setUtilisateur($this);
        }
        return $this;
    }

    public function removeReparation(Reparation $reparation): static
    {
        if ($this->reparations->removeElement($reparation)) {
            if ($reparation->getUtilisateur() === $this) {
                $reparation->setUtilisateur(null);
            }
        }
        return $this;
    }


    /**
     * @return Collection<int, Ticket>
     */
    public function getTicket(): Collection
    {
        return $this->Ticket;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->Ticket->contains($ticket)) {
            $this->Ticket->add($ticket);
            $ticket->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->Ticket->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getUtilisateur() === $this) {
                $ticket->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVous(): Collection
    {
        return $this->Rendez_vous;
    }

    public function addRendezVous(RendezVous $rendezVous): static
    {
        if (!$this->Rendez_vous->contains($rendezVous)) {
            $this->Rendez_vous->add($rendezVous);
            $rendezVous->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRendezVous(RendezVous $rendezVous): static
    {
        if ($this->Rendez_vous->removeElement($rendezVous)) {
            // set the owning side to null (unless already changed)
            if ($rendezVous->getUtilisateur() === $this) {
                $rendezVous->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    
    {
        return $this->Nom_utilisateur . ' ' . $this->Prenom_utilisateur;
    }
}
