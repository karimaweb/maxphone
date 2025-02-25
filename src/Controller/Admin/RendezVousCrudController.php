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
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class RendezVousCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private FlashBagInterface $flashBag;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }

    /**
     * Configuration des champs du CRUD des Rendez-vous
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            //  Vérification de la date du RDV (ne peut pas être dans le passé)
            DateTimeField::new('dateHeureRendezVous')->setLabel('Date et Heure')
                ->setRequired(true)
                ->setHelp('La date du rendez-vous ne peut pas être dans le passé.'),

            //  Choix du statut avec affichage amélioré
            ChoiceField::new('statutRendezVous', 'Statut')
                ->setChoices([
                    'En attente' => 'en attente',
                    'Confirmé' => 'confirmé',
                    'Annulé' => 'annulé',
                ])
                ->setRequired(true)
                // ->allowHtml()
                ->formatValue(fn($value) => match ($value) {
                    'en attente' => '<span class="badge badge-warning"> En attente</span>',
                    'confirmé' => '<span class="badge badge-success">Confirmé</span>',
                    'annulé' => '<span class="badge badge-danger">Annulé</span>',
                    default => $value,
                }),

            // Sélection d'un utilisateur avec un lien direct vers sa fiche
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(true)
                ->formatValue(function ($value, $entity) {
                    return sprintf(
                        '<a href="%s">%s</a>',
                        $this->generateUrl('admin_utilisateur_detail', ['id' => $entity->getUtilisateur()->getId()]),
                        $entity->getUtilisateur()->getNomUtilisateur()
                    );
                }),
                // ->renderAsHtml(),

            //  Description optionnelle
            TextField::new('description', 'Description')
                ->setHelp('Ajoutez une description pour ce rendez-vous')
                ->hideOnIndex(),

            //  Afficher les réparations associées
            AssociationField::new('reparations', 'Réparations associées')
                ->hideOnIndex()
                ->onlyOnDetail(),

            //  Affichage formaté de la date et heure en liste
            TextField::new('formattedDate', 'Date et Heure')
                ->formatValue(fn($value, $entity) => $entity->getFormattedDate())
                // ->renderAsHtml()
                ->onlyOnIndex(),
        ];
    }

    /**
     * 🔹 Permet d'afficher tous les rendez-vous (passés et futurs)
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    }

    /**
     * 🔹 Configuration des filtres de recherche
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
}
