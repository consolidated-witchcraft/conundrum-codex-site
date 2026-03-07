<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Dto\Admin\Features\AdminFeatureSwitcherDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewFeatureSwitcherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'label',
                TextType::class,
            )
            ->add(
            'handle',
            TextType::class,
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminFeatureSwitcherDto::class
        ]);
    }
}
