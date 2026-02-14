<?php

namespace App\Form\Visitor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DummyPaymentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'pay',
                SubmitType::class,
                [
                    'label' => 'Pay Now (DUMMY)',

                ]
            );
    }
}
