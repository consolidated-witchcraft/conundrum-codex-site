<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\EventManager\TicketTypeDto;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'name',
            TextType::class,
            [
                'help' => 'Enter a name for your ticket type, e.g: \'Child\', \'Adult\', \'VIP\', etc'
            ]
        )
        ->add('slug', TextType::class, [
            'help' => 'A ticket type slug'
        ])
        ->add(
            'description',
            TextareaType::class,
            [
                'help' => 'A brief description for this ticket type.',
                'attr' => [
                    'rows' => 10,
                ]
            ]
        )
        ->add(
            'priceCentessimal',
            MoneyType::class,
            [
                'label' => 'Price',
                'html5' => true,
                'help' => 'Enter the cost of a ticket of this type.',
                'currency' => 'GBP',
                'divisor' => 100,
            ]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => TicketTypeDto::class
        ]);
    }
}
