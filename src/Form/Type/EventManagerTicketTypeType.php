<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Services\Ticket\Domain\ValueObjects\TicketFormat;
use App\Services\Ticket\Dto\TicketTypeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventManagerTicketTypeType extends AbstractType implements DataMapperInterface
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
                'help' => 'Enter a name for your ticket type, e.g: \'Child\', \'Adult\', \'VIP\', etc. This will be suffixed with the type  of the ticket.'
            ]
        )
        ->add('slug', TextType::class, [
            'help' => 'A slug for this ticket type. A slug will form part of the SKU for the products generated for this ticket. A slug must be unique within the same event.'
        ])
        ->add(
            'description',
            TextareaType::class,
            [
                'label' => 'Description/Terms and Conditions',
                'help' => 'A brief description for this ticket type, and/or Terms and Conditions',
                'attr' => [
                    'rows' => 10,
                ]
            ]
        )
        ->add(
            'ticketFormats',
            ChoiceType::class,
            [
                'label' => 'Available Ticket formats',
                'choices' => TicketFormat::getKnownFormatsForForm(),
                'expanded' => true,
                'multiple' => true,
                'help' => TicketFormat::generateHelpText(),
                'help_html' => true,
            ]
        )
        ->add(
            'refundableUntil',
            DateTimeType::class,
            [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'help' => 'Tickets will be refundable/returnable until this date. Leave this field blank if tickets are non-refundable.',
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
                case 'refundableUntil':
                    $form->setData($viewData->refundableUntil);
                    break;
                case 'ticketFormats':
                    $form->setData($viewData->ticketTypeFormats);
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
            ticketTypeFormats: $forms['ticketFormats']->getData(),
            refundableUntil: $forms['refundableUntil']->getData(),
            id: (int) $forms['id']->getData(),
        );
    }
}
