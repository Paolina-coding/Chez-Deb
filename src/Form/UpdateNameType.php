<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UpdateNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', TextType::class, [
            'label' => 'Nouveau nom d’utilisateur',
            'required' => true,
            'attr' => [
                'placeholder' => 'Votre nouveau nom affiché',
                'class' => 'form-input'
            ],
        ]);
    }
}