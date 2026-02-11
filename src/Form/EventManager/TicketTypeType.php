<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use App\Dto\EventManager\EventDto;
use App\Dto\EventManager\OrganisationDto;
use App\Dto\EventManager\TicketTypeDto;
use App\Dto\EventManager\TicketTypesDto;
use App\Form\Type\ClueType;
use App\Form\Type\TicketType;
use App\Services\User\Domain\User;
use PHPStan\Type\CompoundType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ticketTypes',
                CollectionType::class,
                [
                    'entry_type' => TicketType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'required' => false,
                    'prototype_options' => [
                        'required' => false,
                        'label' => 'Add a Ticket Type',
                        'attr' => [
                            'placeholder' => 'Configure an available ticket type.',
                        ]


                    ],
                    'entry_options' => [
                        'attr' => [
                            'class' => 'multiadd-row'
                        ],
                    ]
                ]

            )
            ->add('submit', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketTypesDto::class
        ]);
    }
}
