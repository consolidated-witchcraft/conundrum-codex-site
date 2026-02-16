<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Checkout\UserCartLineDto;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserCartLineType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {


        $data = $options['data'];

        $builder
            ->add(
            'quantity',
            NumberType::class,
            [
                'scale' => 0,
                'label' => sprintf("%s - £%d exc. Tax", $data->productName, $data->productPricePreTaxDecimal),
                'attr' => [
                    'data-productpricepretaxcentessimal' => $data->productPricePreTaxCentessimal,
                ]

            ]
        )
        ->add(
            'remove',
            ButtonType::class,
            [
                'attr' => [
                    'class' => 'remove',
                ]
            ]
        )
        ->setDataMapper($this);

    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof UserCartLineDto) {
            return;
        }
        $forms = iterator_to_array($forms);
        $forms['quantity']->setData($viewData->quantity);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $viewData->quantity = (int) $forms['quantity']->getData();
    }
}
