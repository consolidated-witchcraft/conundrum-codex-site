<?php

namespace App\Form\Type\DynamicFormElementTypes;

use App\Form\Type\DieRollType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DynamicDieRollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add(
            'dynamicFieldType',
            HiddenType::class,
            [
                'data' => 'string',
                'label' => false
            ]
        );
        $builder->add(
            'value',
            DieRollType::class,
            [
                'label' => false,
            ]
        );
    }
}
