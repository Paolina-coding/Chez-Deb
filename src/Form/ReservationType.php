<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomClient', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'attr' => ['class' => 'reservation-input'],
            ])
            ->add('telephoneClient', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^0[1-9]([ .-]?[0-9]{2}){4}$/',
                        'message' => 'Numéro invalide',
                    ]),
                ],
                'attr' => ['class' => 'reservation-input'],
            ])
            ->add('datetimeReservation', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan('now'),
                ],
                'attr' => ['class' => 'reservation-input'],
            ])
            ->add('nombrePersonnes', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(1),
                ],
                'attr' => ['class' => 'reservation-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}