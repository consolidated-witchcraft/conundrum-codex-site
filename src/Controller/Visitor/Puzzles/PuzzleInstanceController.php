<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use App\Dto\Visitor\Puzzles\PublishPuzzleInstanceDto;
use App\Form\Visitor\PublishPuzzleInstanceType;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PuzzleInstanceController extends AbstractPuzzleController
{
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/games/{gameSlug}/puzzles/{templateSlug}/{instanceCode}/manage', name: 'app.games.puzzles.instance.manage')]
    public function managePuzzleInstance(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {

        if ($puzzleTemplate->isStatic()) {
            return $this->redirectToRoute($puzzleTemplate->getStaticEditRoute(), [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode()
            ]);
        }

        dd("I godst here");
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/games/{gameSlug}/puzzles/{templateSlug}/{instanceCode}/publish', name: 'app.games.puzzles.instance.publish')]
    public function publishPuzzleInstance(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {
        $dto = new PublishPuzzleInstanceDto();
        $form = $this->createForm(PublishPuzzleInstanceType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $puzzleInstance->setPublicationDate($dto->publicationDate);
            $this->entityManager->persist($puzzleInstance);
            $this->entityManager->flush();
            $this->addFlash('success', 'Publication Date set');
            return $this->redirectToRoute(
                'app.games.puzzles.instance.manage',
                [
                    'gameSlug' => $game->getSlug(),
                    'templateSlug' => $puzzleTemplate->getSlug(),
                    'instanceCode' => $puzzleInstance->getInstanceCode()
                ]
            );
        }
        $pageVars = [
              'pageTitle' => sprintf("Publish puzzle %s", $puzzleInstance->getName()),
              'form' => $form
        ];
        return $this->render('/visitor/puzzleInstances/publish.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GamePlayerVoter::PLAY_GAME, 'game')]
    #[Route('/games/{gameSlug}/puzzles/{templateSlug}/{instanceCode}/play', name: 'app.games.puzzles.instance.play')]
    public function playInstance(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {
        if ($puzzleTemplate->isStatic()) {
            return $this->redirectToRoute($puzzleTemplate->getStaticPlayRoute(), [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode()
            ]);
        } else {
            $pageVars = [
                'pageTitle' => sprintf("Play puzzle %s", $puzzleInstance->getName()),
                'instance' => $puzzleInstance
            ];
            return $this->render('/visitor/puzzleInstances/dynamic/play.html.twig', $this->populatePageVars($pageVars, $request));
        }
    }
}
