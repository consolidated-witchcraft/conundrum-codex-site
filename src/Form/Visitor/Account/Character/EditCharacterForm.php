<?php

declare(strict_types=1);

namespace App\Form\Visitor\Account\Character;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditCharacterForm extends CreateCharacterForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm(builder: $builder, options: $options);

        $builder
            ->add('characterSheet', FileType::class, [
                'required' => false,
                'label' => 'Replace character sheet',
                'attr' => [
                    'accept' => 'image/jpeg,image/png,application/pdf',
                ],
            ])
            ->add('portrait', FileType::class, [
                'required' => false,
                'label' => 'Replace portrait',
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save Character',
            ]);
    }
}
