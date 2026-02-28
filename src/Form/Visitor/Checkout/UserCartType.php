<?php

namespace App\Form\Visitor\Checkout;

use App\Dto\Visitor\Checkout\UserCartDto;
use App\Dto\Visitor\User\UserEditDto;
use App\Form\Type\UserCartLineType;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];

        if (!$data instanceof UserCartDto) {
            return;
        }

        $subform = $builder->create('userCartLines', null , [
            'compound' => true,
            'label' => false
        ]);

        foreach ($data->userCartLines as $userCartLine) {
            $subform->add(
                $userCartLine->getFormSafeSku(),
                UserCartLineType::class,
                [
                    'data' => $userCartLine,
                ]
            );
        }
        $builder->add($subform);
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Pay',
                'attr' => [
                    'class' => 'btn btn-success cta',
                ]
            ]
        );
    }

}
