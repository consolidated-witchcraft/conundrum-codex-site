<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use App\Form\Type\EventManagerTicketTypeType;
use App\Services\Ticket\Dto\TicketTypesDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                    'entry_type' => EventManagerTicketTypeType::class,
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
