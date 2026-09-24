<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PhotoUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('photo', FileType::class, [
            'label' => 'Choisir une photo',
            'mapped' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Veuillez sélectionner une photo.',
                ]),
                new Assert\Image([
                    'maxSize' => '5M',
                    'mimeTypesMessage' => 'Veuillez sélectionner une image valide.',
                ]),
            ],
        ]);
    }
}