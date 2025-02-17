<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CategorieCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nomCategorie', 'Nom de la Catégorie')
                ->setRequired(true)
                ->setHelp('Le nom de la catégorie doit être unique et avoir au moins 3 caractères.'),
            AssociationField::new('parent', 'Catégorie Parent')->autocomplete(),
        ];
    }

    //  Empêcher l'ajout de catégories en double
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Categorie) {
            return;
        }

        // Vérifie que le nom n'est pas vide ou trop court
        if (empty($entityInstance->getNomCategorie()) || strlen($entityInstance->getNomCategorie()) < 3) {
            $this->addFlash('danger', 'Le nom de la catégorie doit contenir au moins 3 caractères.');
            return;
        }

        // Vérifie si la catégorie existe déjà
        $existingCategory = $entityManager->getRepository(Categorie::class)->findOneBy([
            'nomCategorie' => $entityInstance->getNomCategorie()
        ]);

        if ($existingCategory) {
            $this->addFlash('danger', 'Cette catégorie existe déjà.');
            return; // bloque l'ajout
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();

        $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');
    }

    // Empêcher la modification en double
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Categorie) {
            return;
        }

        // Vérifie que le nom n'est pas vide ou trop court
        if (empty($entityInstance->getNomCategorie()) || strlen($entityInstance->getNomCategorie()) < 3) {
            $this->addFlash('danger', 'Le nom de la catégorie doit contenir au moins 3 caractères.');
            return;
        }

        // Vérifie si la catégorie existe déjà et que ce n'est pas la même
        $existingCategory = $entityManager->getRepository(Categorie::class)->findOneBy([
            'nomCategorie' => $entityInstance->getNomCategorie()
        ]);

        if ($existingCategory && $existingCategory->getId() !== $entityInstance->getId()) {
            $this->addFlash('danger', 'Une autre catégorie porte déjà ce nom.');
            return;
        }

        $entityManager->flush();
        $this->addFlash('success', 'La catégorie a été mise à jour avec succès.');
    }
}
