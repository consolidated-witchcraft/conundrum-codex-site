<?php

namespace App\Form\Visitor\Puzzle\AddPuzzle;

use App\Dto\Visitor\Game\AddPuzzle\ChooseGameDto;
use App\Services\Game\Domain\Game;
use App\Services\User\Domain\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseGameType extends AbstractType
{

    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void

    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $builder
                ->add(
                    'template',
                    HiddenType::class,
                )
                ->add(
                    'game',
                    EntityType::class,
                    [
                        'class' => Game::class,
                        'choice_label' => 'name',
                        'choices' => $user->getGamesMastered()
                    ]
                )
                ->add(
                    'puzzleName',
                    TextType::class,
                    [
                        'help' => 'Give your puzzle a useful name, like Temple Switches',
                        'attr' => [
                            'minlength' => 10,
                            'maxlength' => 255
                        ]
                    ]
                )
                ->add(
                    'description',
                    TextAreaType::class,
                    [
                        'help' => 'Give your puzzle an Evocative description that will give your players some flavour.',
                    ]
                )
                ->add(
                    'submit',
                    SubmitType::class,
                    [
                        'label' => 'Next'
                    ]
                )
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChooseGameDto::class,
        ]);
    }
}
