<?php

declare(strict_types=1);

namespace App\Form\Visitor;

use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PuzzleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'search_terms',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'class' => 'inline'
                    ]
                ]
            )
            ->add(
                'categories',
                EntityType::class,
                [
                    'class' => PuzzleCategory::class,
                    'choice_label' => 'label',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                    'attr' => [
                        'class' => 'inline'
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Search'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
