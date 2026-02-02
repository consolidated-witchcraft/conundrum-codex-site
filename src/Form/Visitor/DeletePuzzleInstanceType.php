<?php

namespace App\Form\Visitor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeletePuzzleInstanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'confirm',
            CheckboxType::class,
                [
                    'label' => "Are you sure you want to delete this puzzle?",
                    'help' => 'This action cannot be undone!'
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Delete this Instance',
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
