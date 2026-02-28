<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RevealableClueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'clueText',
            TextType::class,
            [
                'help' => 'The text of your clue.'
            ]
        )
        ->add('revealed', CheckboxType::class, [
            'required' => false,
        ]);
    }
}
