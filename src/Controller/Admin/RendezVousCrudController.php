<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use App\Entity\RendezVous;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;


class RendezVousCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private FlashBagInterface $flashBag;
    // associé une réparation à un rdv résèrvé 
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $requestStack->getSession()->getFlashBag();
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    

    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Liste des Rendez-vous');
    }
    /**
     * Configuration des champs du CRUD des Rendez-vous
     */
    public function configureFields(string $pageName): iterable
    {
    return [
        // Cacher l'ID sur la liste, mais le garder dans le formulaire
        // IdField::new('id')->hideOnIndex(),

        // Cacher la date sur la liste, mais la garder dans le formulaire
         // Ce champ affiche la date formatée dans la liste (sans l'heure si besoin)
         TextField::new('formattedDate', 'Date et Heure')
         ->formatValue(fn($value, $entity) => $entity->getFormattedDate())
         ->onlyOnIndex(),

        DateTimeField::new('dateHeureRendezVous')->setLabel('Date et Heure')
            ->setRequired(true)
            ->setHelp('La date du rendez-vous ne peut pas être dans le passé.')
            ->hideOnIndex(),

            

            AssociationField::new('utilisateur', 'Client')
            ->formatValue(function ($value, $entity) {
            $utilisateur = $entity->getUtilisateur();
            if (!$utilisateur) {
            return 'Aucun client';
        }

        // J'assemble le nom et le prénom dans une seule chaine
        return sprintf(
            '%s %s',
            $utilisateur->getNomUtilisateur(),
            $utilisateur->getPrenomUtilisateur()
        );
    }),

        
            ChoiceField::new('statutRendezVous', 'Statut')
            ->setChoices([
                'Disponible' => 'disponible',
                'Réservé' => 'réservé',
            ])
            ->setRequired(true)
            ->formatValue(fn($value) => match ($value) {
                'disponible' => '<span class="badge badge-success">Disponible</span>',
                'réservé' => '<span class="badge badge-danger">Réservé</span>',
                default => $value,
            }),
        
        TextField::new('description', 'Description')
            ->setHelp('Ajoutez une description pour ce rendez-vous')
            ->hideOnIndex(),

        AssociationField::new('reparations', 'Réparations associées')
            ->hideOnIndex()
            ->onlyOnDetail(),
            TextField::new('creerReparation', 'Créer Réparation')
        ->onlyOnIndex()
        ->setVirtual(true) // Indique qu'il n'y a pas de propriété réelle
        ->formatValue(function($value, $entity) {
        // Générer le bouton/lien ici
        
   

                // On récupère l'ID du RendezVous
                $rdvId = $entity->getId();

                // On génère l’URL vers ReparationCrudController, action "new"
                // en passant "rdvId" en paramètre
                $url = $this->adminUrlGenerator
                    ->setController(ReparationCrudController::class)
                    ->setAction('new')
                    ->set('rdvId', $rdvId)
                    ->generateUrl();

                // On renvoie un bouton Bootstrap qui pointe vers cette URL
                return sprintf(
                    '<a class="btn btn-sm btn-primary" href="%s">Créer Réparation</a>',
                    $url
                );
            }),
       
    ];
    }


    /**
     *  Permet d'afficher tous les rendez-vous (réservé et futurs)
     */
    public function createIndexQueryBuilder(
        SearchDto $searchDto, 
        EntityDto $entityDto, 
        FieldCollection $fields, 
        FilterCollection $filters
    ): QueryBuilder {
        // Récupérer le QueryBuilder par défaut
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    
        // Alias principal (souvent "entity")
        $alias = $qb->getRootAliases()[0];
    
        // 1) Filtrer sur le statut = "réservé"
        $qb->andWhere(sprintf('%s.statutRendezVous = :statut', $alias))
           ->setParameter('statut', 'réservé');
    
        // 2) Filtrer pour n'afficher que les rendez-vous futurs (après maintenant)
        // $qb->andWhere(sprintf('%s.dateHeureRendezVous > :now', $alias))
        //    ->setParameter('now', new \DateTime());
    
        return $qb;
    }
    

    /**
     *  Configuration des filtres de recherche
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('dateHeureRendezVous'); // Permet de filtrer les RDV en fonction de leur date
    }

    /**
     *  Vérification et sauvegarde d'un Rendez-vous avec messages flash
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) {
            return;
        }

        //  Vérification de la date (ne peut pas être dans le passé)
        $now = new \DateTime();
        if ($entityInstance->getDateHeureRendezVous() < $now) {
            $this->flashBag->add('danger', 'La date du rendez-vous ne peut pas être dans le passé.');
            return;
        }

        // Vérification du statut
        $statutValide = ['en attente', 'confirmé', 'annulé'];
        if (!in_array($entityInstance->getStatutRendezVous(), $statutValide)) {
            $this->flashBag->add('danger', 'Statut invalide.');
            return;
        }

        //  Vérification qu'un utilisateur est bien associé
        if (!$entityInstance->getUtilisateur()) {
            $this->flashBag->add('danger', 'Un client doit être associé à ce rendez-vous.');
            return;
        }

        parent::persistEntity($entityManager, $entityInstance);
        $this->flashBag->add('success', 'Rendez-vous ajouté avec succès.');
    }

    /*
     * Mise à jour d'un rendez-vous avec messages flash
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) {
            return;
        }

        //  Vérification de la date
        $now = new \DateTime();
        if ($entityInstance->getDateHeureRendezVous() < $now) {
            $this->flashBag->add('danger', 'La date du rendez-vous ne peut pas être dans le passé.');
            return;
        }

        parent::updateEntity($entityManager, $entityInstance);
        $this->flashBag->add('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     *  Suppression sécurisée d'un rendez-vous avec messages flash
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) {
            return;
        }

        //  Vérifier si le RDV est encore associé à des réparations
        if (!$entityInstance->getReparations()->isEmpty()) {
            $this->flashBag->add('danger', 'Impossible de supprimer un rendez-vous lié à une réparation.');
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
        $this->flashBag->add('success', 'Rendez-vous supprimé avec succès.');
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW); // je désactive le bouton "Add Ticket" admin n'a pas besoin de créer un ticket
    }
}