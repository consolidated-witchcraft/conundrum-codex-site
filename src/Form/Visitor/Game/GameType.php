<?php

declare(strict_types=1);

namespace App\Form\Visitor\Game;

use App\Dto\Visitor\Game\GameDto;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GameType extends AbstractType
{
    public const array VALID_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'webp'];

    public function __construct(private GameEntityServiceInterface $gameService) {

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'help' => 'Give your game a short, punchy, memorable name.'
                ]
            )
            ->add(
                'heroImageUrl',
                FileType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File(
                            maxSize: '5m',
                            extensions: self::VALID_IMAGE_TYPES,
                            extensionsMessage: 'Please upload a valid image file. Valid filestypes are:',
                        )
                    ],
                    'label' => 'Hero Image'
                ]
            )
            ->add(
                'slug',
                HiddenType::class,
                [
                    'attr' => [

                        'value' => $this->gameService->getRandomUnusedSlug()
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'help' => 'Describe your game here! Max 1024 characters.',
                    'attr' => [
                        'rows' => 10
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Save game'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameDto::class,
        ]);
    }
}
