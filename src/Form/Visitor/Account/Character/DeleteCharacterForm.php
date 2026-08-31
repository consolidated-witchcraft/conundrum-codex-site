<?php

declare(strict_types=1);

namespace App\Form\Visitor\Account\Character;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteCharacterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirmation', CheckboxType::class, [
                'required' => true,
                'label' => 'I really want to delete this character.',
                'help' => 'This will also delete all of the character notes and cannot be undone.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Delete Character',
                'attr' => [
                    'class' => 'btn btn-danger',
                ],
            ]);
    }
}
