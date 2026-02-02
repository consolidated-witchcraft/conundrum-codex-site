<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\PuzzleInstance;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleTemplateController;
use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Game\AddPuzzle\ChooseGameDto;
use App\Form\Type\DynamicFormElementTypes\DynamicDieRollType;
use App\Form\Type\DynamicFormElementTypes\DynamicTextType;
use App\Form\Visitor\Puzzle\AddPuzzle\ChooseGameType;
use App\Security\GameManagerVoter;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\User\Domain\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class AddPuzzleController extends AbstractPuzzleController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/puzzles/templates/{templateSlug}/add-to-game/step-1', name: 'app.puzzles.instance.add')]
    public function addToGame(string $templateSlug, Request $request): Response {
        $template = $this->puzzleTemplateService->getTemplateBySlug($templateSlug);

        if (!$template) {
            throw $this->createNotFoundException('Template not found');
        }
        $user = $this->getUser();
        if (null === $user) {
            return $this->redirectToRoute('app.auth.login', ['_target_path' => '/dashboard']);
        }
        if ($user instanceof User) {
            $dto = new ChooseGameDto($template->getSlug());
            $form = $this->createForm(ChooseGameType::class, $dto);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $serializedDto = $this->serializer->serialize(
                    new AddPuzzleStepOneDto(
                        templateSlug: $templateSlug,
                        gameSlug: $dto->game->getSlug(),
                        puzzleName: $dto->puzzleName,
                        puzzleDescription: $dto->description
                    ),
                    'json'
                );
                $session = $request->getSession();
                $session->set(self::ADD_TO_GAME_SESSION_KEY, $serializedDto);
                $this->addFlash('success', 'Puzzle added! Now to configure it.');

                return $template->isStatic() ? $this->redirectToRoute($template->getStaticCreateRoute(), ['gameSlug' => $dto->game->getSlug()]) :
                    $this->redirectToRoute(
                        'app.puzzles.instance.configure',
                        [
                            'templateSlug' => $templateSlug,
                            'gameSlug' => $dto->game->getSlug()
                        ]);
            }
            $pageVars = [
                'pageTitle' => sprintf("Add a %s puzzle to game", $template->getTitle()),
                'form' => $form
            ];

        } else {
            throw new \Exception("User is of incorrect type");
        }

        return $this->render('/visitor/puzzles/templates/addToGame/step1.html.twig', $this->populatePageVars($pageVars, $request));
    }

    /**
     * @throws \Exception
     */
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/puzzles/templates/{templateSlug}/add-to-game/{gameSlug}/step-2', name: 'app.puzzles.instance.configure')]
    public function configurePuzzle(
        PuzzleTemplate $puzzleTemplate,
        Game $game,
        Request $request
    ): Response
    {
        $session = $request->getSession();
        $sessionValues = $session->get(PuzzleTemplateController::ADD_TO_GAME_SESSION_KEY);
        $optionsDto = $this->serializer->deserialize($sessionValues, AddPuzzleStepOneDto::class, 'json');

        $dto = DynamicPuzzleInstanceDto::makeFromPuzzleTemplate(
            puzzleName: $optionsDto->puzzleName,
            puzzleDescription: $optionsDto->puzzleDescription,
            game: $game,
            puzzleTemplate: $puzzleTemplate,
        );
        $dto->setInstanceCode($this->puzzleInstanceService->generateNewInstanceCode($puzzleTemplate));

        $form = $this->generateTemplateForm($puzzleTemplate, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            die("Submitting");
            /*
            $instance = new PuzzleInstance(
                instanceCode: $this->puzzleTemplateService->generatePuzzleSlug(puzzleName: $optionsDto->puzzleName, puzzleClass: PuzzleInstance::class),
                name: $optionsDto->puzzleName,
                game: $game,
                templateSlug: $puzzleTemplate->getSlug(),
                description: $optionsDto->puzzleName,
            );
            $instance->setConfig($dto->toArray());
            $success = true;
            $violations = $this->validator->validate($instance);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($instance);
                $this->entityManager->flush();
                $this->addFlash('success', 'Puzzle added!');
                return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
            }*/
        }

        $pageVars = [
            'pageTitle' => sprintf("Configure %s puzzle", $puzzleTemplate->getTitle()),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/addToGame/configure.html.twig', $this->populatePageVars($pageVars, $request));
    }

    private function generateTemplateForm(PuzzleTemplate $puzzleTemplate, DynamicPuzzleInstanceDto $dto): FormInterface {
        $builder = $this->createFormBuilder();

        foreach ($puzzleTemplate->getConfiguration() as $configurationOption) {
            switch ($configurationOption->getType()) {
                case 'string':
                    $builder->add(
                        $configurationOption->getConfigName(),
                        DynamicTextType::class
                    );
                    break;
                case 'dieRoll':
                    $builder->add(
                        $configurationOption->getConfigName(),
                        DynamicDieRollType::class
                    );
                    break;
                default:
                    dump($configurationOption->getType());
            }
        }
        $builder->setData($dto);
        $builder->add('submit', SubmitType::class);
        return $builder->getForm();
    }
}
