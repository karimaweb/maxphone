<?php
namespace App\Form;

use App\Entity\Reparation;
use App\Entity\Produit;
use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReparationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('diagnostic', TextType::class, [
                'label' => 'Diagnostic',
                'required' => true,
            ])
            ->add('dateHeureReparation', DateTimeType::class, [
                'label' => 'Date de Réparation',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('statutReparation', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en_attente',
                    'En cours' => 'en_cours',
                    'Terminé' => 'termine',
                ],
                'required' => true,
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p'); // Charge tous les produits
                },
                'choice_label' => 'libelleProduit',
                'placeholder' => 'Sélectionner un produit',
                'required' => true,
            ])
            ->add('rendezVous', EntityType::class, [
                'class' => RendezVous::class,
                'choice_label' => 'id',
                'label' => 'Rendez-vous lié',
                'placeholder' => 'Sélectionner un rendez-vous',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reparation::class,
        ]);
    }
}
