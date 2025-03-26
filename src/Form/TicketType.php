<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Reparation;
use App\Repository\ReparationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('objetTicket', TextType::class, [
                'label' => 'Objet ',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'objet est obligatoire.']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'L\'objet doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'L\'objet ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])

            // ->add('statutTicket', ChoiceType::class, [
            //     'label' => 'Statut du ticket',
            //     'choices' => [
            //         'Ouvert' => 'ouvert',
            //         'En cours' => 'en_cours',
            //         'Résolu' => 'resolu',
            //         'Fermé' => 'ferme',
            //     ],
            //     'constraints' => [
            //         new Assert\NotBlank(['message' => 'Le statut est obligatoire.'])
            //     ],
            //     'attr' => ['class' => 'form-select']
            // ])

            ->add('dateCreationTicket', DateTimeType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date est obligatoire.']),
                    new Assert\Type(\DateTimeInterface::class)
                ],
                'attr' => ['class' => 'form-control']
            ])

            ->add('descriptionTicket', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.'
                    ])
                ],
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])

            ->add('reparation', EntityType::class, [
                'class' => Reparation::class,
                'label' => 'Réparation liée',
                'choice_label' => 'diagnostic',
                'query_builder' => function (ReparationRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('r')
                        ->where('r.utilisateur = :user')
                        ->setParameter('user', $user);
                },
                'placeholder' => 'Sélectionnez une réparation',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Veuillez sélectionner une réparation.'])
                ],
                'attr' => ['class' => 'form-select']
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'user' => null,
        ]);
    }
}
