<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class CategorieCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;


    public function createIndexQueryBuilder(
    SearchDto $searchDto,
    EntityDto $entityDto,
    FieldCollection $fields,
    FilterCollection $filters
): QueryBuilder {
    $qb = $this->entityManager->getRepository(Categorie::class)
        ->createQueryBuilder('c')
        ->where('c.parent IS NULL')
        ->orderBy('c.nomCategorie', 'ASC');

    return $qb;
}
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('index', 'Les catégories ');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nomCategorie', 'Nom de la Catégorie')
                ->setRequired(true)
                ->setHelp('Le nom de la catégorie doit être unique et avoir au moins 3 caractères.')
                ->formatValue(function ($value, $entity) {
                    if (method_exists($entity, 'getNiveau')) {
                        return str_repeat('— ', $entity->getNiveau()) . $value;
                    }
                    return $value;
                }),
            AssociationField::new('parent', 'Catégorie Parent')->autocomplete(),
        ];
    }

    // Empêcher l'ajout de catégories en double
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
        $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
    }
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if (!$entityInstance instanceof Categorie) {
        return;
    }

    // Vérifie si la catégorie est parent d'autres catégories
    $childCategories = $entityManager->getRepository(Categorie::class)->findBy([
        'parent' => $entityInstance
    ]);

    if (count($childCategories) > 0) {
        $this->addFlash('danger', 'Impossible de supprimer cette catégorie car elle possède des sous-catégories.');
        return;
    }

    // Si la catégorie est une sous-catégorie, on peut gérer différemment selon le besoin :
    // ici, on la supprime simplement (ou tu peux la détacher du parent si nécessaire)

    $entityManager->remove($entityInstance);
    $entityManager->flush();

    $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
}

}
