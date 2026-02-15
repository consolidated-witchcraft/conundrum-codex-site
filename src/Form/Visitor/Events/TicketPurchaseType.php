<?php

declare(strict_types=1);

namespace App\Form\Visitor\Events;

use App\Dto\Visitor\Events\TicketPurchaseDto;

use App\Dto\Visitor\Events\TicketTypePurchaseTypeDto;
use App\Form\Type\TicketTypePurchaseType;
use App\Form\Type\VisitorTicketSelectionType;
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
        foreach ($data->ticketTypes as $ticketTypeSlug => $ticketType) {
            $builder->add(
                $ticketTypeSlug,
                TicketTypePurchaseType::class,
                [
                    'label' => sprintf("%s Ticket", $ticketType->getName()),
                    'data' => TicketTypePurchaseTypeDto::makeFromTicketType($ticketType)
                ]);
        }

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Proceed to Payment'
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
        foreach ($forms['ticketTypes']->getData() as $key => $datum) {
            $strippedKey = str_replace('_quantity', '', $key);
            $viewData->ticketTypes[$strippedKey]['quantity'] = (int) $datum;
        }
    }

}
