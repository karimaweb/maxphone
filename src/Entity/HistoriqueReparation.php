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
    if (!empty(trim($statut)) && $statut !== 'Aucun(e)') { // ✅ Éviter les valeurs vides et "Aucun(e)"
        $this->statutHistoriqueReparation = ucfirst($statut);
    }
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
    public function __toString(): string
    {
        return sprintf(
            '%s - %s - %s', 
            $this->reparation ? $this->reparation->getDiagnostic() : 'Diagnostic inconnu',
            $this->statutHistoriqueReparation,
            $this->commentaire ?: 'Pas de commentaire'
        );
    }
    public function getFormattedClient(): string
    {
        // On accède à l'entité Reparation pour récupérer l'utilisateur
        $reparation = $this->getReparation();

        if ($reparation && $reparation->getUtilisateur()) {
            return $reparation->getUtilisateur()->getNomUtilisateur() . ' ' . $reparation->getUtilisateur()->getPrenomUtilisateur();
        }

        return 'Aucun client';
    }
    public function getProduit(): ?string
    {
    // Vérifie si la réparation et le produit sont définis
    $reparation = $this->getReparation();

    if ($reparation && $reparation->getProduit() instanceof Produit) {
        // Retourner le libellé du produit si l'objet Produit est valide
        return $reparation->getProduit()->getLibelleProduit();
    }

    return 'Aucun produit';
}

// récuperer l'historique de réparation
public function getHistoriqueSimplifie(): string
{
    $reparation = $this->getReparation();

    if (!$reparation) {
        return '<span style="color:gray;">Aucune réparation associée</span>';
    }

    $client = $reparation->getUtilisateur();
    $clientNom = $client ? $client->getNomUtilisateur() . ' ' . $client->getPrenomUtilisateur() : '<span style="color:orange;">Aucun client</span>';
    $produitNom = $reparation->getProduit() ? $reparation->getProduit() : 'Produit inconnu';

    $historiqueUnique = [];
    $dernierStatut = null;

    foreach ($reparation->getHistoriques() as $historiqueItem) {
        $statut = ucfirst(trim($historiqueItem->getStatutHistoriqueReparation()));

        //  Ignorer les statuts vides ou "Aucun(e)"
        if (empty($statut) || $statut === 'Aucun(e)') {
            continue;
        }

            if ($dernierStatut !== $statut) {
                $historiqueUnique[] = sprintf(
                
                $historiqueItem->getDateMajReparation()->format('d/m/Y H:i'),
                $produitNom,
                $statut
            );
            $dernierStatut = $statut;
            }
        }

    //  Toujours retourner une chaîne de caractères
        if (empty($historiqueUnique)) {
        return '<span style="color:gray;">Aucun historique disponible</span>';
        }

        return "<strong> $clientNom</strong><br>" . implode('<br>', $historiqueUnique) . "<hr>";
    }
}
