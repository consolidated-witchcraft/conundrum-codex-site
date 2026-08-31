<?php

declare(strict_types=1);

namespace App\Form\Visitor\Account\Character;

use App\Dto\Visitor\User\CharacterDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCharacterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('class', TextType::class)
            ->add('race', TextType::class)
            ->add('level', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('portrait', FileType::class, [
                'required' => true,
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
            ])
            ->add('characterSheet', FileType::class, [
                'required' => false,
                'label' => 'Character sheet',
                'attr' => [
                    'accept' => 'image/jpeg,image/png,application/pdf',
                ],
            ])
            ->add('biography', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 8,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create Character',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CharacterDto::class,
        ]);
    }
}
