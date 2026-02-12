<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class UserCartLineType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add(
            'quantity',
            NumberType::class,
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

    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        // TODO: Implement mapFormsToData() method.
    }
}
