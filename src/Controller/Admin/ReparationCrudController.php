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

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->security = $security; // Récupération de l'utilisateur connecté
    }

    public static function getEntityFqcn(): string
    {
        return Reparation::class;
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
            
            AssociationField::new('rendezVous', 'Rendez-vous')
                ->formatValue(function ($value, $entity) {
                    if ($value) {
                        // Retourner la date formatée au lieu de l'ID
                        return $value->getDateHeureRendezVous()->format('d/m/Y H:i');
                    }
                    return 'Aucun rendez-vous';
                })
                ->renderAsHtml()
                ->autocomplete(),

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
            return;
        }

        $flashBag = $this->requestStack->getSession()->getFlashBag();

        // Vérification du diagnostic
        if (strlen($entityInstance->getDiagnostic()) < 5) {
            $flashBag->add('danger', 'Le diagnostic doit contenir au moins 5 caractères.');
            return;
        }

        // Vérification de la date (ne peut pas être dans le passé)
        $now = new \DateTime();
        if ($entityInstance->getDateHeureReparation() < $now) {
            $flashBag->add('danger', 'La date de réparation ne peut pas être dans le passé.');
            return;
        }

        $rendezVous = $entityInstance->getRendezVous();
        $client = $rendezVous ? $rendezVous->getUtilisateur() : $entityInstance->getUtilisateur();

        // Vérification qu'un client est bien associé
        if (!$rendezVous && !$client) {
            $flashBag->add('danger', 'Veuillez associer un client ou un rendez-vous à cette réparation.');
            return;
        }

        // Vérification du RendezVous (optionnel : date non passée, pas déjà associé à une autre réparation, etc.)
        if ($rendezVous) {
            if ($rendezVous->getDateHeureRendezVous() < $now) {
                $flashBag->add('danger', 'Le rendez-vous sélectionné est déjà passé.');
                return;
            }
            $existingRep = $entityManager->getRepository(Reparation::class)
                ->findOneBy(['rendezVous' => $rendezVous]);
            if ($existingRep) {
                $flashBag->add('danger', 'Ce rendez-vous est déjà associé à une autre réparation.');
                return;
            }
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

        // Création automatique d'un historique
        $historique = new HistoriqueReparation();
        $historique->setReparation($entityInstance);
        $historique->setStatutHistoriqueReparation($entityInstance->getStatutReparation());
        $historique->setDateMajReparation(new \DateTime());
        $entityManager->persist($historique);

        // Sauvegarde de la réparation
        parent::persistEntity($entityManager, $entityInstance);

        $flashBag->add('success', 'Réparation ajoutée avec succès.');
    }

    /**
     * Mettre à jour l'état des tickets liés à la réparation avec messages flash
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Reparation) {
            return;
        }

        $flashBag = $this->requestStack->getSession()->getFlashBag();

        // Vérifier que la réparation n'est pas "terminée" si on veut empêcher tout retour en arrière
        // (Optionnel - à activer si vous souhaitez bloquer toute modif après statut "terminé")
        if ($entityInstance->getStatutReparation() === 'terminé') {
            // Ex. : Empêcher toute mise à jour après la clôture
            // $flashBag->add('danger', 'Impossible de modifier une réparation déjà terminée.');
            // return;
        }

        // Récupérer l'ancien statut avant modification
        $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $ancienStatut = $originalData['statutReparation'] ?? "Inconnu";

        // Vérifier si le statut a changé avant d'ajouter un historique
        $nouveauStatut = $entityInstance->getStatutReparation();
        if ($ancienStatut !== $nouveauStatut) {
            // Vérifier si un historique similaire existe déjà pour éviter les doublons
            $dernierHistorique = $entityManager->getRepository(HistoriqueReparation::class)
                ->findOneBy(['reparation' => $entityInstance], ['dateMajReparation' => 'DESC']);

            // Exemple : on compare uniquement la dernière "transition"
            $transition = sprintf('%s → %s', ucfirst($ancienStatut), ucfirst($nouveauStatut));
            if ($dernierHistorique && $dernierHistorique->getStatutHistoriqueReparation() === $transition) {
                // Ne pas ajouter de doublon
            } else {
                $historique = new HistoriqueReparation();
                $historique->setReparation($entityInstance);
                $historique->setStatutHistoriqueReparation($transition);
                $historique->setDateMajReparation(new \DateTime());

                // Enrichir d'un commentaire
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
        $ticket = $entityManager->getRepository(\App\Entity\Ticket::class)
        ->findOneBy(['reparation' => $entityInstance]);

    // 2) Mettre le ticket en "Résolu" si la réparation passe en "terminé"
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
