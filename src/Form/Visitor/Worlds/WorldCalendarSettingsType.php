<?php

declare(strict_types=1);

namespace App\Form\Visitor\Worlds;

use App\Dto\Visitor\Worlds\WorldCalendar\WorldCalendarSettingsDto;
use App\Form\Type\WorldCalendar\WorldCalendarDayOfWeekType;
use App\Form\Type\WorldCalendar\WorldCalendarMonthType;
use App\Services\World\Domain\WorldCalendarMonth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorldCalendarSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'hoursInDay',
            NumberType::class,
            [
                'attr' => [
                    'min' => 1,
                    'max' => 100
                ],
                'help' => 'How many hours in a day in your world?'
            ]
        )->add(
            'daysOfWeek',
            CollectionType::class,
            [
                'entry_type' => WorldCalendarDayOfWeekType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'required' => false,
                'help' => 'Add the days of your world\'s weeks, in order. You may add up to fifty. Each Day must have a name and an abbreviation, and each must be unique within this world',
                'prototype_options' => [
                    'required' => false,
                    'label' => 'Add a Day of the week',

                    'attr' => [

                    ]


                ],
                'entry_options' => [
                    'attr' => [
                        'class' => 'multiadd-row'
                    ]
                ]
            ]
        )
            ->add(
                'months',
                CollectionType::class,
                [
                    'entry_type' => WorldCalendarMonthType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'required' => false,
                    'help' => 'Add the months of your world\'s year, in order. You may add up to fifty. Each month must have a name, an abbreviation and a number of days within it, and each must be unique within this world',
                    'prototype_options' => [
                        'required' => false,
                        'label' => 'Add a Month of the Year',

                        'attr' => [

                        ]


                    ],
                    'entry_options' => [
                        'attr' => [
                            'class' => 'multiadd-row'
                        ]
                    ]
                ]
            );
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorldCalendarSettingsDto::class,
        ]);
    }
}
