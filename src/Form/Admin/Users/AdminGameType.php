<?php

namespace App\Form\Admin\Users;

use App\Dto\Admin\User\AdminUserGameDto;
use App\Services\Game\Domain\Game;
use App\Services\Game\Service\GameService;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminGameType extends AbstractType
{
    public function __construct(
        private readonly GameServiceInterface $gameService
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $games = $this->gameService->findAllGames(false);
        $builder
            ->add(
                'games',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(static function () use ($games) {
                        $gamesList = [];
                        foreach ($games as $game) {
                            $gamesList[$game->getName()] = $game->getId();
                        }
                        return $gamesList;
                    })
                ]
            )
            ->add('submit', SubmitType::class, [])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminUserGameDto::class,
        ]);
    }
}
