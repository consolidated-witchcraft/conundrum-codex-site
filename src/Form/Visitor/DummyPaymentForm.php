<?php

namespace App\Form\Visitor;

use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DummyPaymentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('simulatedPaymentStatus',
                ChoiceType::class,
                [
                    'choices' => array_flip(PaymentStatus::KNOWN_PAYMENT_STATUSES)
                ])
            ->add(
                'pay',
                SubmitType::class,
                [
                    'label' => 'Pay Now',

                ]
            );
    }
}
