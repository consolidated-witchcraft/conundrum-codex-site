<?php

declare(strict_types=1);

namespace App\Form\Visitor\Puzzle\Static\Casebook;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectDto;
use App\Form\Type\ClueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType as CasebookSubjectTypeEntity;
class CasebookSubjectType extends AbstractType
{
    public const array VALID_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'webp'];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File(
                            maxSize: '5m',
                            extensions: self::VALID_IMAGE_TYPES,
                            extensionsMessage: 'Please upload a valid image file. Valid filestypes are:',
                        )
                    ],
                ]
            )
            ->add(
                'revealed',
                CheckboxType::class,
                [
                    'label' => 'Revealed?',
                    'required' => false,
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'help' => 'What is your Casebook subject\'s name?'
                ]
            )
            ->add(
                'type',
                EntityType::class,
                [
                    'class' => CasebookSubjectTypeEntity::class,
                    'choice_label' => 'label',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'help' => 'Add a brief overview of this subject\'s background.',

                    'attr' => [
                        'rows' => 15,
                    ]

                ]
            )


            ->add(
                'clues',
                CollectionType::class,
                [
                    'entry_type' => ClueType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'required' => false,
                    'prototype_options' => [
                        'required' => false,
                        'label' => 'Add a Clue',
                        'help' => 'Add the detail of this clue, to be revealed when the players discover it. For example; He was seen in the office just before the murder, or This gem burns with a yellow flame.',
                        'attr' => [
                            'placeholder' => 'Add a clue about this subject that you can reveal at-will.',
                        ]


                    ],
                    'entry_options' => [
                        'attr' => [
                            'class' => 'multiadd-row'
                        ]
                    ]
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'attr' => ['class' => 'btn-primary'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CasebookSubjectDto::class
        ]);
    }
}
