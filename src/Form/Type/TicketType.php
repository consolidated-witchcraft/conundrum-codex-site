<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Services\Ticket\Dto\TicketTypeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'id',
            HiddenType::class,
            [
                'required' => false,
            ]
        )
        ->add(
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
            'ticketFormats',
            ChoiceType::class,
            [
                'choices' => [
                    'eTicket' => 'e_ticket',
                    'Physical Ticket' => 'physical_ticket',
                ],
                'expanded' => true,
                'multiple' => true,
                'help' => 'Physical Tickets can be posted to the customer. eTickets are delivered via App and E-Mail'
            ]
        )
        ->add(
            'priceCentessimal',
            MoneyType::class,
            [
                'label' => 'Pre-Tax Price',
                'html5' => true,
                'help' => 'Enter the pre-tax cost of a ticket of this type.',
                'currency' => 'GBP',
                'divisor' => 100,
            ]
        )
        ->add(
            'initialQuantity',
            NumberType::class,
            [
                'label' => 'Initial Quantity Available',
                'html5' => true,
            ]
        );
        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketTypeDto::class
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }
        if (!$viewData instanceof TicketTypeDto) {
            return;
        }
        foreach ($forms as $key => $form) {
            switch ($key) {
                case 'id':
                    $form->setData($viewData->id);
                    break;
                case 'name':
                    $form->setData($viewData->name);
                    break;
                case 'slug':
                    $form->setData($viewData->slug);
                    break;
                case 'description':
                    $form->setData($viewData->description);
                    break;
                case 'priceCentessimal':
                    $form->setData($viewData->priceCentessimal);
                    break;
                case 'ticketFormats':
                    $form->setData($viewData->ticketFormats);
                    break;
                case 'initialQuantity':
                    $form->setData($viewData->initialQuantity);
                    break;
            }
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $viewData = new TicketTypeDto(
            name: $forms['name']->getData(),
            slug: $forms['slug']->getData(),
            description: $forms['description']->getData(),
            priceCentessimal: (int) ($forms['priceCentessimal']->getData()),
            initialQuantity: (int) $forms['initialQuantity']->getData(),
            ticketFormats: $forms['ticketFormats']->getData(),
            id: (int) $forms['id']->getData(),
        );
    }
}
