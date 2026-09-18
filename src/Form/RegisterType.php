<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'attr' => ['class' => 'form-input', 'placeholder' => 'email@example.com']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom d’utilisateur',
                'required' => false,
                'attr' => ['class' => 'form-input', 'placeholder' => 'Votre nom (optionnel)']
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6]),
                ],
                'attr' => ['class' => 'form-input', 'placeholder' => 'Votre mot de passe']
            ]);
    }
}