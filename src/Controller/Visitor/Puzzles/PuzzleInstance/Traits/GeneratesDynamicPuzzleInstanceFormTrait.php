<?php

namespace App\Controller\Visitor\Puzzles\PuzzleInstance\Traits;

use App\Form\Mapper\DynamicInstanceDataMapper;
use App\Form\Type\DieRollType;
use App\Form\Type\KeyValueType;
use App\Form\Type\RevealableClueFormType;
use App\Services\PuzzleInstance\Domain\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

trait GeneratesDynamicPuzzleInstanceFormTrait
{
    protected function generateTemplateForm(
        FormBuilderInterface $builder,
        DynamicInstanceDataMapper $dynamicInstanceDataMapper,
        PuzzleTemplate $puzzleTemplate,
        DynamicPuzzleInstanceDto $dto
    ): FormInterface {

        foreach ($puzzleTemplate->getConfiguration() as $configurationOption) {
            switch ($configurationOption->getType()) {
                case ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        TextType::class,
                        [
                            'help' => $configurationOption->getHelpText()
                        ]
                    );
                    break;

                case ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        DieRollType::class,
                        [
                            'help' => $configurationOption->getHelpText()
                        ]
                    );
                    break;

                case ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        NumberType::class,
                        [
                            'help' => $configurationOption->getHelpText(),
                            'html5' => true,
                            'scale' => 0,
                        ]
                    );
                    break;

                case ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        ChoiceType::class,
                        [
                            'choices' => [
                                'True' => true,
                                'False' => false,
                            ],
                            'expanded' => true,
                            'help' => $configurationOption->getHelpText()
                        ]
                    );
                    break;


                case ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => TextType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'required' => false,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add a String',
                                'attr' => [
                                    'placeholder' => 'Add a String',
                                ]


                            ],
                            'entry_options' => [
                                'attr' => [
                                    'class' => 'multiadd-row'
                                ],
                                'help' => $configurationOption->getHelpText()
                            ]
                        ]
                    );
                    break;
                case ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => NumberType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'required' => false,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add an Integer',
                                'attr' => [
                                    'placeholder' => 'Add an Integer',
                                ]


                            ],
                            'entry_options' => [
                                'attr' => [
                                    'class' => 'multiadd-row'
                                ],
                                'help' => $configurationOption->getHelpText()
                            ]
                        ]
                    );
                    break;
                case ValidPuzzleConfigOptionTypes::BOOLEAN_ARRAY_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => ChoiceType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'required' => false,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add a Boolean',
                                'attr' => [
                                    'placeholder' => 'Add a Boolean',
                                ]


                            ],
                            'entry_options' => [
                                'choices' => [
                                    'True' => true,
                                    'False' => false
                                ],
                                'expanded' => true,
                                'attr' => [
                                    'class' => 'multiadd-row'
                                ],
                                'help' => $configurationOption->getHelpText()
                            ]
                        ]
                    );
                    break;
                case ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => KeyValueType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'required' => false,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add a Key/Value Pair',


                            ],
                            'entry_options' => [
                                'attr' => [
                                    'class' => 'multiadd-row'
                                ],
                                'help' => $configurationOption->getHelpText()
                            ]
                        ]
                    );
                    break;
                case ValidPuzzleConfigOptionTypes::CLUE_ARRAY_KEY:
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => RevealableClueFormType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'required' => false,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add a Revealable Clue',


                            ],
                            'entry_options' => [
                                'attr' => [
                                    'class' => 'multiadd-row'
                                ],
                                'help' => $configurationOption->getHelpText()
                            ]
                        ]
                    );
                    break;
                default:
                    dd($configurationOption->getConfigName());

            }
        }

        $builder->add('submit', SubmitType::class);
        $builder->setDataMapper($dynamicInstanceDataMapper);
        $builder->setData($dto);
        return $builder->getForm();
    }
}
