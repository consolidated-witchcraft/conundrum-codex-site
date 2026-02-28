<?php

namespace App\Form\Visitor\Checkout;

use App\Form\Type\PostalAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeliveryInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'receiptEmail',
                EmailType::class,
                [
                    'help' => 'The e-mail address to which your receipt/e-tickets should be sent.',
                ]
            )
            ->add(
            'deliveryAddress',
        PostalAddressType::class,
        )->add(
            'billingAddress',
            PostalAddressType::class,
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Review your Order',
                'attr' => [
                    'class' => 'btn btn-success cta',
                ]
            ]
        );
    }
}
