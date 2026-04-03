<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\PuzzleInstance;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleInstance\Traits\GeneratesDynamicPuzzleInstanceFormTrait;
use App\Dto\Visitor\Puzzles\PublishPuzzleInstanceDto;
use App\Form\Visitor\DeletePuzzleInstanceType;
use App\Form\Visitor\PublishPuzzleInstanceType;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PuzzleInstanceController extends AbstractPuzzleController
{
    use GeneratesDynamicPuzzleInstanceFormTrait;

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

        $dto = DynamicPuzzleInstanceDto::makeFromPuzzleInstance($puzzleInstance);
        $form = $this->generateTemplateForm(
            builder: $this->createFormBuilder(),
            dynamicInstanceDataMapper: $this->dynamicInstanceDataMapper,
            puzzleTemplate: $puzzleTemplate,
            dto: $dto,
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $puzzleInstance->setConfig($dto->config);


            $success = true;
            $violations = $this->validator->validate($puzzleInstance);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($puzzleInstance);
                $this->entityManager->flush();
                $this->addFlash('success', 'Puzzle edited!');
                return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit puzzle %s", $puzzleInstance->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/puzzleInstances/edit.html.twig', $this->populatePageVars($pageVars, $request));
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
            $this->puzzleInstanceTemplateService->saveInstance($puzzleInstance);
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
        $user = $this->getUser();
        if ($puzzleTemplate->isStatic()) {
            return $this->redirectToRoute($puzzleTemplate->getStaticPlayRoute(), [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode()
            ]);
        } else {
            if ($puzzleInstance instanceof DynamicPuzzleInstanceInterface) {
                $templateSlug = $puzzleTemplate->getSlug();
                $relativeCssPath = sprintf('contrib/puzzles/%s/css/%s.css', $templateSlug, $templateSlug);
                $absoluteCssPath = sprintf('%s/public/%s', $this->getParameter('kernel.project_dir'), $relativeCssPath);

                $puzzleCssHref = file_exists($absoluteCssPath) ? '/' . $relativeCssPath : null;
                $pageVars = [
                    'pageTitle' => sprintf("Play puzzle %s", $puzzleInstance->getName()),
                    'templateSlug' => $puzzleTemplate->getSlug(),
                    'instanceCode' => $puzzleInstance->getInstanceCode(),
                    'elementName' => $puzzleTemplate->getElementName(),
                    'puzzleCssHref' => $puzzleCssHref,
                ];
                return $this->render('/visitor/puzzleInstances/dynamic/play.html.twig', $this->populatePageVars($pageVars, $request));
            }
            throw new MismappedPuzzleTemplateException("Puzzle instance is not static, but does not implement DynamicPuzzleInstanceInterface");
        }
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/games/{gameSlug}/puzzles/{templateSlug}/{instanceCode}/delete', name: 'app.games.puzzles.instance.delete')]
    public function deleteInstance(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ) {
        $form = $this->createForm(DeletePuzzleInstanceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->puzzleInstanceTemplateService->deleteInstance($puzzleInstance);
            $this->addFlash('success', 'Puzzle instance deleted!');
            return $this->redirectToRoute('app.games.manage', [
                'gameSlug' => $game->getSlug(),
            ]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Really delete puzzle %s", $puzzleInstance->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/puzzleInstances/delete.html.twig', $this->populatePageVars($pageVars, $request));

    }
}
