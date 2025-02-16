<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class ProduitCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    // Récupérer uniquement les produits en vente
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->entityManager->getRepository(Produit::class)->createQueryBuilder('p');

        return $qb->where('p.typeProduit = :type')
                  ->setParameter('type', 'vente');
    }

    public function configureFields(string $pageName): iterable
    {
        $typeProduitField = ChoiceField::new('typeProduit')
            ->setChoices(['Vente' => 'vente', 'Réparation' => 'réparation']);

        // Champs communs
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('libelleProduit', 'Nom du produit'),
            $typeProduitField,
            AssociationField::new('categorie', 'Catégorie'),
        ];

        // Afficher Prix et Stock UNIQUEMENT si Type Produit = Vente
        if ($pageName === 'edit' || $pageName === 'new') {
            $fields[] = NumberField::new('prixUnitaire', 'Prix Unitaire')
                ->setRequired(false)
                ->onlyOnForms();

            $fields[] = NumberField::new('qteStock', 'Quantité en Stock')
                ->setRequired(false)
                ->onlyOnForms();
        }

        return $fields;
    }
}
