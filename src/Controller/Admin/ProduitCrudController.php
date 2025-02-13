<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('id', 'ID')->hideOnForm(), // Ne pas afficher dans le formulaire
            TextField::new('libelleProduit', 'Nom du produit'),
            NumberField::new('prixUnitaire', 'Prix Unitaire'),
            TextField::new('typeProduit', 'Type de produit'),
            NumberField::new('qteStock', 'Quantité en stock'),
            AssociationField::new('categorie', 'Catégorie'),
            AssociationField::new('utilisateur', 'Utilisateur')->setRequired(false),
        ];
    }
}
