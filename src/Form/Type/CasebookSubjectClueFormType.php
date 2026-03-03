<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectClueDto;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasebookSubjectClueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'title',
            TextType::class,
            [
                'help' => 'Enter a short name for your clue.'
            ]
        )
        ->add(
            'body',
            TextareaType::class,
            [
                'help' => 'Enter the detailed content of the clue',
                'attr' => [
                    'rows' => 10,
                ]
            ]
        )
        ->add(
            'type',
            EntityType::class,
            [
                'class' => CasebookSubjectClueFormType::class,
                'choice_label' => 'label',
                'help' => 'Select a type for your clue.'
            ]
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => CasebookSubjectClueDto::class
        ]);
    }
}
