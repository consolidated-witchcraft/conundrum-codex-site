<?php

namespace App\Form\Visitor\Puzzle\Static\Casebook;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasebookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'brief',
                TextareaType::class,
                [
                    'help' => 'Create a brief for this casebook, sketching out the mystery your players must solve.'
                ]
            )
            ->add(
                'next',
                SubmitType::class,
                [
                    'attr' => ['class' => 'btn-success'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CasebookDto::class,
        ]);
    }
}
