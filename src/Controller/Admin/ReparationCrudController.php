<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityRepository;
use App\Entity\Reparation;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;
use App\Entity\HistoriqueReparation;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ReparationCrudController extends AbstractCrudController
{
    private $requestStack;
    private $security;



    public static function getEntityFqcn(): string
    {
        return Reparation::class;
    }
    //associer une réparation à un rdv
    private EntityManagerInterface $em;

    public function __construct(
        
        // Récupération de l'utilisateur connecté
        EntityManagerInterface $em,
        RequestStack $requestStack,
        Security $security
    ) {
        // On stocke $em dans $this->em
        $this->em = $em;

        // On stocke $requestStack
        $this->requestStack = $requestStack;

        // On stocke $security
        $this->security = $security;
    }
  

    // Selon votre version EasyAdmin, c'est souvent createEntity()
    // ou createNewEntity() :
    public function createEntity(string $entityFqcn)
    {
        $reparation = new Reparation();

        // 1) Récupérer rdvId dans l'URL
        $rdvId = $this->requestStack->getCurrentRequest()->query->get('rdvId');
        if ($rdvId) {
            // 2) Charger le RendezVous en base
            $rdv = $this->em->getRepository(RendezVous::class)->find($rdvId);
            if ($rdv) {
                // 3) Associer le rendez-vous
                $reparation->setRendezVous($rdv);

                $client = $rdv->getUtilisateur(); 
            if ($client) {
                // Utiliser le champ "utilisateur" de Reparation
                $reparation->setUtilisateur($client);
            }
        }
    }

        return $reparation;
    }
    
    public function monAction()
    {
        // On utilise $this->em pour associer un rdv à une réparation
        $produit = $this->em->getRepository(Produit::class)->find($id);
      
    }
    public function configureFields(string $pageName): iterable
    {
        
        return [
            TextField::new('diagnostic')
                ->setLabel('Diagnostic')
                ->setRequired(true)
                ->setHelp('Minimum 5 caractères'),

            DateTimeField::new('dateHeureReparation')
                ->setLabel('Date de dépôt')
                ->setRequired(true)
                ->setHelp('Ne peut pas être dans le passé'),
        
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(false)
                ->formatValue(function ($value, $entity) {
                    return $value
                        ? $value->getNomUtilisateur() . ' ' . $value->getPrenomUtilisateur()
                        : '<span class="badge badge-danger">Aucun client</span>';
                })
                ->renderAsHtml()
                ->setHelp(
                    '<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\UtilisateurCrudController" 
                        class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un client</a>'
                )
                ->autocomplete(),
                AssociationField::new('produit', 'Produit en réparation')
                ->setFormTypeOptions([
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                                  ->where('p.typeProduit = :type')
                                  ->setParameter('type', 'réparation');
                    }
                ])
                ->setRequired(false)
                ->setHelp(
                    '<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ProduitCrudController"
                        class="btn btn-primary" target="_blank" style="margin-top:5px;">Ajouter un produit</a>'
                ),

            ChoiceField::new('statutReparation', 'Statut')
                ->setChoices([
                    'En attente du diagnostic' => 'en attente',
                    'Diagnostic en cours'      => 'diagnostic en cours',
                    'Pièce commandée'          => 'pièce commandée',
                    'Pièce reçue'              => 'pièce reçue',
                    'Début de réparation'      => 'début de réparation',
                    'Test final en cours'      => 'test final en cours',
                    'Réparation terminée'      => 'terminé',
                ])
                ->renderExpanded(false)
                ->allowMultipleChoices(false)
                ->setRequired(true),

       

            AssociationField::new('tickets', 'Ticket associé')
                ->onlyOnDetail(),
        ];
    }

    /**
     * Vérification et sauvegarde d'une réparation avec messages flash
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
    if (!$entityInstance instanceof Reparation) {
        
        parent::persistEntity($entityManager, $entityInstance);
        return;
    }

    $flashBag = $this->requestStack->getSession()->getFlashBag();

    // 1) Contrôles existants
    // Vérification du diagnostic (au moins 5 caractères)
    if (strlen($entityInstance->getDiagnostic()) < 5) {
        $flashBag->add('danger', 'Le diagnostic doit contenir au moins 5 caractères.');
        return;
    }

    // Vérification de la date de réparation (pas dans le passé)
    $now = new \DateTime();
    if ($entityInstance->getDateHeureReparation() < $now) {
        $flashBag->add('danger', 'La date de réparation ne peut pas être dans le passé.');
        return;
    }

    // Récupération du rendez-vous et/ou du client
    $rendezVous = $entityInstance->getRendezVous();
    $client = $rendezVous ? $rendezVous->getUtilisateur() : $entityInstance->getUtilisateur();

    // Si vous exigez qu'un client soit associé dans tous les cas
    if (!$client) {
        $flashBag->add('danger', 'Veuillez associer un client (via un rendez-vous ou directement).');
        return;
    }

    // 2) Vérification du RendezVous
    if ($rendezVous) {
        // a) Empêcher un RDV passé
        if ($rendezVous->getDateHeureRendezVous() < $now) {
            $flashBag->add('danger', 'Le rendez-vous sélectionné est déjà passé.');
            return;
        }

        // b) Vérifier qu'il n'y a pas déjà une réparation pour ce RDV
        $existingRep = $entityManager->getRepository(Reparation::class)
            ->findOneBy(['rendezVous' => $rendezVous]);
        if ($existingRep) {
            $flashBag->add('danger', 'Ce rendez-vous est déjà associé à une autre réparation.');
            return;
        }

        // c) **Contrôle de correspondance de date** entre RDV et réparation
        $dateRdv = $rendezVous->getDateHeureRendezVous()->format('Y-m-d');
        $dateReparation = $entityInstance->getDateHeureReparation()->format('Y-m-d');

        if ($dateRdv !== $dateReparation) {
            $flashBag->add('danger', 'La date de la réparation doit correspondre à la date du rendez-vous.');
            return;
        }
    }

    // 3) Création automatique d’un ticket
    $ticket = new Ticket();
    $ticket->setObjetTicket("Réparation avec rdv");
    $ticket->setDescriptionTicket("Réparation ajoutée en magasin.");
    $ticket->setStatutTicket("En cours");
    $ticket->setDateCreationTicket(new \DateTime());
    $ticket->setUtilisateur($client);
    $ticket->setReparation($entityInstance);
    $entityManager->persist($ticket);

    // 4) Création d'un historique
    $historique = new HistoriqueReparation();
    $historique->setReparation($entityInstance);
    $historique->setStatutHistoriqueReparation($entityInstance->getStatutReparation());
    $historique->setDateMajReparation(new \DateTime());
    $entityManager->persist($historique);

    // 5) Persistance finale de la réparation
    parent::persistEntity($entityManager, $entityInstance);

    $flashBag->add('success', 'Réparation ajoutée avec succès.');
    }


    
     //Mettre à jour l'état des tickets liés à la réparation avec messages flash
     
     public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
     {
         if (!$entityInstance instanceof Reparation) {
             return;
         }
     
         $flashBag = $this->requestStack->getSession()->getFlashBag();
     
         // Récupérer l'ancien statut avant modification
         $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
         $ancienStatut = $originalData['statutReparation'] ?? "Inconnu";
     
         //  Empêcher toute modification si la réparation est déjà terminée
         if ($ancienStatut === 'terminé') {
             $flashBag->add('danger', 'Impossible de modifier une réparation déjà terminée.');
             return;
         }
     
         // Vérifier si le statut a changé avant d'ajouter un historique
         $nouveauStatut = $entityInstance->getStatutReparation();
         if ($ancienStatut !== $nouveauStatut) {
             $dernierHistorique = $entityManager->getRepository(HistoriqueReparation::class)
                 ->findOneBy(['reparation' => $entityInstance], ['dateMajReparation' => 'DESC']);
     
             $transition = sprintf('%s → %s', ucfirst($ancienStatut), ucfirst($nouveauStatut));
     
             if (!$dernierHistorique || $dernierHistorique->getStatutHistoriqueReparation() !== $transition) {
                 $historique = new HistoriqueReparation();
                 $historique->setReparation($entityInstance);
                 $historique->setStatutHistoriqueReparation($transition);
                 $historique->setDateMajReparation(new \DateTime());
     
                 if ($entityInstance->getUtilisateur()) {
                     $commentaire = sprintf(
                         'Mise à jour du statut : "%s" → "%s"',
                         ucfirst($ancienStatut),
                         ucfirst($nouveauStatut)
                     );
                     $historique->setCommentaire($commentaire);
                 }
     
                 $entityManager->persist($historique);
             }
         }
     
         // Mise à jour du ticket lié si la réparation est terminée
         $ticket = $entityManager->getRepository(\App\Entity\Ticket::class)
             ->findOneBy(['reparation' => $entityInstance]);
     
         if ($nouveauStatut === 'terminé' && $ancienStatut !== 'terminé') {
             if ($ticket) {
                 $ticket->setStatutTicket('Résolu');
             }
         }
     
         parent::updateEntity($entityManager, $entityInstance);
         $entityManager->flush();
     
         $flashBag->add('success', 'Réparation mise à jour avec succès.');
     }
     
    /**
     * Vérifier avant suppression d'une réparation avec messages flash
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        if ($entityInstance instanceof Reparation) {
            // Vérifier si la réparation est liée à un ticket non résolu
            $ticket = $entityManager->getRepository(Ticket::class)
                ->findOneBy(['reparation' => $entityInstance]);

            if ($ticket && $ticket->getStatutTicket() !== 'Résolu') {
                $flashBag->add('danger', 'Impossible de supprimer une réparation liée à un ticket non résolu.');
                return;
            }
        }
        $flashBag->add('success', 'Réparation supprimée avec succès.');
        parent::deleteEntity($entityManager, $entityInstance);
    }
      //) éthode interne pour ajouter un historique (si vous souhaitez la réutiliser)
    private function ajouterHistorique(Reparation $reparation, EntityManagerInterface $entityManager): void
    {
        $historique = new HistoriqueReparation();
        $historique->setReparation($reparation);
        $historique->setStatutHistoriqueReparation($reparation->getStatutReparation());
        $historique->setCommentaire('Mise à jour par l’admin.');
        $historique->setDateMajReparation(new \DateTime());
        $historique->setTechnicien($this->security->getUser()); // Enregistre l'admin connecté
        $entityManager->persist($historique);
        $entityManager->flush();
    }
    
}
