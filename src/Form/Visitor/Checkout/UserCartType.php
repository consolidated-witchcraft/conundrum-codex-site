<?php

namespace App\Form\Visitor\Checkout;

use App\Dto\Visitor\User\UserEditDto;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCartType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];

        if (!$data instanceof UserCart) {
            return;
        }

        foreach ($data->getUserCartLines() as $userCartLine) {
            $builder->add(
                $userCartLine->getProduct()->getFormSafeSku(),
                NumberType::class,
                [
                    'label' => $userCartLine->getProduct()->getName(),
                ]
            );
            $builder->add('remove',
                ButtonType::class,
                [
                    'attr' => [
                        'data-target-sku' => $userCartLine->getProduct()->getFormSafeSku(),
                    ]
                ]
            );
        }
        $builder->setDataMapper($this);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof UserCart) {
            return;
        }
        $forms = iterator_to_array($forms);
        foreach ($forms as $key => $form) {
            $sku = str_replace(':', '.', $key);

            $form->setData($viewData->getUserCartLineBySku($sku)?->getQuantity());
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        foreach ($forms as $key => $form) {
            if ($key === 'remove') {
                continue;
            }
            $sku = str_replace(':', '.', $key);
            $cartLine = $viewData->getUserCartLineBySku($sku);
            if ($cartLine instanceof UserCartLine) {
                $cartLine->setQuantity((int) $form->getData());
            }
        }
    }
}
