<?php

declare(strict_types=1);

namespace App\Form\Visitor\Events;

use App\Dto\Visitor\Events\TicketPurchaseDto;

use App\Dto\Visitor\Events\TicketTypePurchaseTypeDto;
use App\Form\Type\TicketTypePurchaseType;
use App\Services\Ticket\Domain\TicketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketPurchaseType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        /**
         * @var TicketPurchaseDto $data
         */
        $data = $options['data'];


        /**
         * What needs to happen here?
         *
         * 1. Loop through all the ticket types in an event and add a buy field for it.
         */
        /**
         * @var TicketTypePurchaseTypeDto $ticketTypePurchaseType
         */
        foreach ($data->ticketTypes as $ticketTypeSlug => $ticketTypePurchaseType) {
            $builder->add(
                $ticketTypeSlug,
                TicketTypePurchaseType::class,
                [
                    'label' => sprintf("%s Ticket", $ticketTypePurchaseType->ticketType->getName()),
                    'data' => $ticketTypePurchaseType,
                    'help' => sprintf("<p>%s</p><p>%s</p>", $ticketTypePurchaseType->ticketType->getEvent()->getStartDate()->format('Y-m-d H:i'), $ticketTypePurchaseType->ticketType->getEvent()->getEndDate()->format('Y-m-d H:i')),
                    'help_html' => true
                ]);
        }

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Proceed to Delivery Information'
            ]
        );
        $builder->setDataMapper($this);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketPurchaseDto::class,
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof TicketPurchaseDto) {
            return;
        }
        $forms = iterator_to_array($forms);
        foreach ($forms as $key => $form) {
            switch ($key) {
                case 'ticketTypes':
                    $form->setData($viewData->ticketTypes);
            }
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        foreach ($forms as $key => $form) {
            if ($key === 'submit') {
                continue;
            }
            /**
             * @var TicketTypePurchaseTypeDto $ticketTypePurchaseTypeDto
             */
            $ticketTypePurchaseTypeDto = $form->getData();
        }
    }

}
