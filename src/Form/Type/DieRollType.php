<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DieRollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {


        $choices = [
            4 => 4,
            6 => 6,
            8 => 8,
            10 => 10,
            12 => 12,
            20 => 20,
            100 => 100
        ];


        $builder->add(
            'x',
            NumberType::class,
            [
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'step' => 1
                ],
                'html5' => true,
                'help' => 'How many dice should be rolled?'
            ]
        )
        ->add(
            'd',
            ChoiceType::class,
            [
                'choices' => $choices,
                'help' => 'How many sides should each die have?'
            ]
        )
        ->add(
            'p',
            NumberType::class,
            [
                'required' => false,

                'attr' => [
                    'value' => 0,
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
                'html5' => true,
                'help' => 'Add this number to the total of rolls (optional)'
            ]
        );

    }
}
