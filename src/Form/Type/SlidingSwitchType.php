<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectClueDto;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlidingSwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'enabled',
            CheckboxType::class,
            [
                'label' => 'Enable/Disable',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
