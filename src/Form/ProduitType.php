<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelleProduit', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix Unitaire (€)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('qteStock', NumberType::class, [
                'label' => 'Quantité en Stock',
                'attr' => ['class' => 'form-control']
            ])
            ->add('typeProduit', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => [
                    'Vente' => 'vente',
                    'Réparation' => 'réparation',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('categorie', EntityType::class, [
                'class' => 'App\Entity\Categorie',
                'choice_label' => 'nomCategorie',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
