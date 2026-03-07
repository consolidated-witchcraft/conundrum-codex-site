<?php

declare(strict_types=1);

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminCacheClearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'confirm',
            CheckboxType::class,
            [
                'required' => true,
                'label' => 'Are you sure you want to clear the cache?',
                'help' => 'This will have a significant performance impact until the cache is rebuilt.'
            ]
        )
        ->add(
            'submit',
            SubmitType::class,
            [
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ]
        );

    }
}
