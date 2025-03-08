<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

use App\Entity\Reparation;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;
use App\Entity\HistoriqueReparation;

class ReparationCrudController extends AbstractCrudController
{
    private $requestStack;
    private $security;

    public function __construct(Security $security,RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->security = $security; // recuperer l'utilisateur connécté 
    }

    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }

    /**
     * Vérification et sauvegarde d'une réparation avec messages flash
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reparation) {
            return;
        }

        $flashBag = $this->requestStack->getSession()->getFlashBag();

        //  Vérification du diagnostic
        if (strlen($entityInstance->getDiagnostic()) < 5) {
            $flashBag->add('danger', 'Le diagnostic doit contenir au moins 5 caractères.');
            return;
        }

        //  Vérification de la date (ne peut pas être dans le passé)
        $now = new \DateTime();
        if ($entityInstance->getDateHeureReparation() < $now) {
            $flashBag->add('danger', 'La date de réparation ne peut pas être dans le passé.');
            return;
        }

        $rendezVous = $entityInstance->getRendezVous();
        $client = $rendezVous ? $rendezVous->getUtilisateur() : $entityInstance->getUtilisateur();

        //  Vérification qu'un client est bien associé
        if (!$rendezVous && !$client) {
            $flashBag->add('danger', 'Veuillez associer un client ou un rendez-vous à cette réparation.');
            return;
        }
        
        // Création automatique d’un ticket si pas de rendez-vous
        if (!$rendezVous) {
            $ticket = new Ticket();
            $ticket->setObjetTicket("Réparation sans RDV");
            $ticket->setDescriptionTicket("Réparation ajoutée en magasin.");
            $ticket->setStatutTicket("En cours");
            $ticket->setDateCreationTicket(new \DateTime());
            $ticket->setUtilisateur($client);
            $ticket->setReparation($entityInstance);
            $entityManager->persist($ticket);
        }

        parent::persistEntity($entityManager, $entityInstance);
        $flashBag->add('success', 'Réparation ajoutée avec succès.');
    }

    /**
     * Configuration des champs du CRUD
     */
    public function configureFields(string $pageName): iterable 
{
    return [
        TextField::new('diagnostic')->setLabel('Diagnostic')
            ->setRequired(true)
            ->setHelp('Minimum 5 caractères'),

        DateTimeField::new('dateHeureReparation')->setLabel(' Date de Réparation')
            ->setRequired(true)
            ->setHelp('Ne peut pas être dans le passé'),

        AssociationField::new('rendezVous', ' Rendez-vous')
            ->setRequired(false)
            ->formatValue(function ($value, $entity) {
                return $value ? $value->getDateHeureRendezVous()->format('d/m/Y H:i') : '<span class="badge badge-danger">Sans RDV</span>';
            })
            ->renderAsHtml()
            ->autocomplete(),

        ChoiceField::new('statutReparation', 'Statut')
            ->setChoices([
                'En attente' => 'en attente',
                'En cours' => 'en cours',
                'Terminé' => 'terminé',
            ])
            ->setRequired(true)
            ->formatValue(function ($value, $entity) {
                return match ($value) {
                    'en attente' => '<span class="badge badge-warning">En attente</span>',
                    'en cours' => '<span class="badge badge-info">En cours</span>',
                    'terminé' => '<span class="badge badge-success">Terminé</span>',
                    default => $value,
                };
            }),

        AssociationField::new('utilisateur', 'Client')
            ->setRequired(false)
            ->formatValue(function ($value, $entity) {
                return $value ? $value->getNomUtilisateur() . ' ' . $value->getPrenomUtilisateur() : '<span class="badge badge-danger">Aucun client</span>';
            })
            ->renderAsHtml()
            ->setHelp('<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\UtilisateurCrudController" class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un client</a>')
            ->autocomplete(),

            AssociationField::new('produit', 'Produit en réparation')
            ->setRequired(false)
            ->setHelp('<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ProduitCrudController" class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un produit</a>'),
        
        AssociationField::new('tickets', ' Ticket associé')
            ->onlyOnDetail(),
    ];
}


    /**
     * Mettre à jour l'état des tickets liés à la réparation avec messages flash
     */
    // Dans ReparationCrudController.php

public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof Reparation) {
        // Vérifier et enregistrer les tickets liés à la réparation
        foreach ($entityInstance->getTickets() as $ticket) {
            $ticket->setStatutTicket(
                strtolower($entityInstance->getStatutReparation()) === 'terminé' ? 'Résolu' : 'En cours'
            );
            $entityManager->persist($ticket);
        }

        // Enregistrement de l'historique de la réparation
        $historique = new HistoriqueReparation();
        $historique->setReparation($entityInstance); // Association avec la réparation
        $historique->setStatutHistoriqueReparation($entityInstance->getStatutReparation()); // Statut actuel
        $historique->setCommentaire('Mise à jour par l’admin.');
        $historique->setDateMajReparation(new \DateTime()); // Date actuelle

        // Ajouter le technicien (utilisateur connecté)
        // $technicien = $this->security->getUser();
        // if ($technicien) {
        //     $historique->setTechnicien($technicien); // Enregistre l'admin connecté
        // }

        $entityManager->persist($historique);
        $entityManager->flush();
    }

    parent::updateEntity($entityManager, $entityInstance);
}

    /**
     * Vérifier avant suppression d'une réparation avec messages flash
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();

        if ($entityInstance instanceof Reparation) {
            $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['reparation' => $entityInstance]);

            if ($ticket && $ticket->getStatutTicket() !== 'Résolu') {
                $flashBag->add('danger', 'Impossible de supprimer une réparation liée à un ticket non résolu.');
                return;
            }
        }

        $flashBag->add('success', 'Réparation supprimée avec succès.');
        parent::deleteEntity($entityManager, $entityInstance);
    }
    private function ajouterHistorique(Reparation $reparation, EntityManagerInterface $entityManager): void
    {
        $historique = new HistoriqueReparation();
        $historique->setReparation($reparation);
        $historique->setStatut($reparation->getStatut());
        $historique->setCommentaire('Mise à jour par l’admin.');
        $historique->setDateMiseAJour(new \DateTime());
        $historique->setTechnicien($this->security->getUser()); // Enregistre l'admin connecté

        $entityManager->persist($historique);
        $entityManager->flush();
    }
    

}
