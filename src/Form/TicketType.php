<?php 

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;



class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objetTicket', TextType::class, [
                'label' => 'Objet du Ticket',
                'required' => true,
            ])
            ->add('descriptionTicket', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('statutTicket', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Ouvert' => 'ouvert',
                    'En cours' => 'en_cours',
                    'Résolu' => 'resolu',
                    'Fermé' => 'ferme',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('dateCreationTicket', DateTimeType::class, [
                'label' => 'Date de Création',
                'widget' => 'single_text',
            ])
            ->add('reparation', EntityType::class, [
                'class' => Reparation::class,
                'choice_label' => 'diagnostic',
                'label' => 'Réparation associée',
                'required' => false,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'emailUtilisateur',
                'label' => 'Utilisateur',
            ]);
    }
}
