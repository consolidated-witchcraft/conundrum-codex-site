<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\Static;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleTemplateController;
use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookDto;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookFormType;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;


class CasebookPuzzleController extends AbstractPuzzleController
{

    /**
     * @throws ExceptionInterface
     */
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/puzzles/templates/static/casebook/add-to-game/{gameSlug}/create', name: 'app.puzzles.static.casebook.create')]
    public function createPuzzle(
        Game $game,
        Request $request
    ): Response {
        $session = $request->getSession();
        $sessionValues = $session->get(PuzzleTemplateController::ADD_TO_GAME_SESSION_KEY);
        $options = $this->serializer->deserialize($sessionValues, AddPuzzleStepOneDto::class, 'json');
        $dto = new CasebookDto($options->puzzleName);
        $form = $this->createForm(CasebookFormType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $casebook = new Casebook(
                name: $dto->puzzleName,
                slug: $this->puzzleService->generatePuzzleSlug($options->puzzleName, Casebook::class),
                game: $game,
                brief: $dto->brief
            );
            $this->entityManager->persist($casebook);
            $this->entityManager->flush();
            $this->addFlash(type: 'success', message: "Casebook created");

            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => Casebook::TEMPLATE_SLUG,
                'instanceCode' => $casebook->getInstanceCode(),
            ]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Set up Casebook puzzle '%s'", $options->puzzleName),
            'form' => $form
        ];
        return $this->render('/visitor/puzzleInstances/casebook/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzleInstances/static/{templateSlug}/{instanceCode}/edit', name: 'app.puzzles.static.casebook.edit')]
    public function editInstance(
        Game $game,
        PuzzleTemplate $template,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => sprintf("Edit Casebook puzzle '%s'", $puzzleInstance->getName()),
            'casebook' => $puzzleInstance,
            'game' => $game,
            'template' => $template,
            'breadcrumbs' => [
                'game' => [
                    'label' => $game->getName(),
                    'link' => $this->generateUrl('app.games.manage', ['gameSlug' => $game->getSlug()])
                ],
                'puzzle' => [
                    'label' => $puzzleInstance->getName(),
                    'link' => $this->generateUrl('app.puzzles.static.casebook.edit', [
                        'gameSlug' => $game->getSlug(),
                        'templateSlug' => Casebook::TEMPLATE_SLUG,
                        'instanceCode' => $puzzleInstance->getInstanceCode(),
                        'active' => true
                    ])
                ],
            ],
        ];
        return $this->render('/visitor/puzzleInstances/casebook/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GamePlayerVoter::PLAY_GAME, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}', name: 'app.puzzles.static.casebook.play')]
    public function play(
        Game $game,
        PuzzleTemplate $template,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => $puzzleInstance->getName(),
            'casebook' => $puzzleInstance,
            'game' => $game,
            'template' => $template,
            'breadcrumbs' => [
                'game' => [
                    'label' => $game->getName(),
                    'link' => $this->generateUrl('app.games.play', ['gameSlug' => $game->getSlug()])
                ],
                'puzzle' => [
                    'label' => $puzzleInstance->getName(),
                    'link' => $this->generateUrl('app.games.puzzles.instance.play', [
                        'gameSlug' => $game->getSlug(),
                        'templateSlug' => Casebook::TEMPLATE_SLUG,
                        'instanceCode' => $puzzleInstance->getInstanceCode(),
                        'active' => true
                    ])
                ],
            ]
        ];
        return $this->render('/visitor/puzzleInstances/casebook/play.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
