<?php

namespace App\Form\Type;

use App\Dto\Visitor\Events\TicketTypePurchaseTypeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class TicketTypePurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var TicketTypePurchaseTypeDto $data
         */
        $data = $options['data'];
        $ticketType = $data->ticketType;
        $formatChoices = [];
        foreach ($ticketType->getTicketTypeProductVariantMaps() as $productVariantMap) {
            $formatChoices[$productVariantMap->getProductVariant()->getProductVariantName()] = $productVariantMap->getProductVariant()->getSku();
        }
        $builder->add(
            'quantity',
                NumberType::class,
            [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                ],
                'help' => $ticketType->getDescription()
            ]
        )
        ->add('selectedFormatSlug', ChoiceType::class, [
            'label' => 'Ticket Format',
            'choices' => $formatChoices,
            'help' => 'Electronic tickets are delivered via e-mail and can be downloaded again from the website. Physical tickets must be posted.',
        ]);
    }
}
