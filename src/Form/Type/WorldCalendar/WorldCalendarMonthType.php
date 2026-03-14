<?php

declare(strict_types=1);

namespace App\Form\Type\WorldCalendar;

use App\Dto\Visitor\Worlds\UserWorldCreationDto;
use App\Services\World\Domain\WorldCalendarMonth;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorldCalendarMonthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
        )
        ->add(
            'abbreviation',
            TextType::class,
        )
            ->add(
                'delta',
                NumberType::class,
                [
                    'attr' => [
                        'min' => 0,

                        'step' => 1,
                    ]
                ]
            )
            ->add(
                'numberOfDays',
                    NumberType::class,
                    [
                        'attr' => [
                            'min' => WorldCalendarMonth::MINIMUM_NUMBER_OF_DAYS,
                            'max' => WorldCalendarMonth::MAXIMUM_NUMBER_OF_DAYS,
                            'step' => 1,
                        ]

                    ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserWorldCreationDto::class,
        ]);
    }
}
