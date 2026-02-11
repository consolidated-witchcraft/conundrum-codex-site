<?php

declare(strict_types=1);

namespace App\Form\Visitor\Events;

use App\Dto\Visitor\Events\TicketPurchaseDto;
use App\Dto\Visitor\User\PostalAddressFieldDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketPurchaseType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        /**
         * @var TicketPurchaseDto $data
         */
        $data = $options['data'];
        $builder->add(
            'customerName',
            TextType::class,
        );
        $builder->add(
            'customerEmail',
            EmailType::class,
        );

        $subform = $builder->create('ticketTypes', null, [
            'compound' => true,
            'label' => 'Choose your tickets',
            'attr' => [
                'class' => 'number-spinner-wrapper',
            ]
        ]);
        foreach ($data->ticketTypes as $ticketTypeSlug => $ticketTypeArray) {
            $subform->add(
                sprintf("%s_quantity", $ticketTypeSlug),
                NumberType::class,
                [
                    'label' => sprintf(" %s - £%s", $ticketTypeArray['name'], ($ticketTypeArray['priceCentessimal'])/100),
                    'required' => false,
                    'attr' => [
                        'min' => 0,
                        'max' => 10,
                        'class' => 'number-spinner',
                        'is' => 'number-spinner',
                        'step' => 1,
                        'length' => 2,
                        'placeholder' => 1,
                    ],
                    'data' => 0,
                    'html5' => true,
                    'help' => $ticketTypeArray['description'],
                    'mapped' => true,

                ]
            );
        }
        $builder->add($subform);
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
                case 'customerName':
                    $form->setData($viewData->customerName);
                    break;
                case 'customerEmail':
                    $form->setData($viewData->customerEmail);
                    break;
                case 'ticketTypes':
                    $form->setData($viewData->ticketTypes);
            }
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData->customerName = $forms['customerName']->getData();
        $viewData->customerEmail = $forms['customerEmail']->getData();

        foreach ($forms['ticketTypes']->getData() as $key => $datum) {
            $strippedKey = str_replace('_quantity', '', $key);
            $viewData->ticketTypes[$strippedKey]['quantity'] = (int) $datum;
        }
    }
}
