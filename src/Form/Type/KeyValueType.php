<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectClueDto;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'key',
            TextType::class,
            [
                'help' => 'The key of your entry (Machine Name).'
            ]
        )
        ->add(
            'value',
            TextType::class,
            [
                'help' => 'The value of your entry (Human-readable Label).'
            ]
        );


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
