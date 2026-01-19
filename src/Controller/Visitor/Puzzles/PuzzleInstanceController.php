<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Game\AddPuzzle\ChooseGameDto;
use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleConfigDto;
use App\Dto\Visitor\Puzzles\PublishPuzzleInstanceDto;
use App\Form\Type\DieRollType;
use App\Form\Visitor\PublishPuzzleInstanceType;
use App\Form\Visitor\Puzzle\AddPuzzle\ChooseGameType;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\Puzzle\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\PuzzleInstance;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\User\Domain\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PuzzleInstanceController extends AbstractPuzzleController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/puzzles/templates/{templateSlug}/add-to-game', name: 'app.puzzles.template.add')]
    public function addToGame(string $templateSlug, Request $request): Response {
        $template = $this->puzzleService->getTemplateBySlug($templateSlug);

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
                    new AddPuzzleStepOneDto(templateSlug: $templateSlug, gameSlug: $dto->game->getSlug(), puzzleName: $dto->puzzleName),
                    'json'
                );
                $session = $request->getSession();
                $session->set(self::ADD_TO_GAME_SESSION_KEY, $serializedDto);
                $this->addFlash('success', 'Puzzle added! Now to configure it.');

                return $template->isStatic() ? $this->redirectToRoute($template->getStaticCreateRoute(), ['gameSlug' => $dto->game->getSlug()]) :
                    $this->redirectToRoute(
                        'app.puzzles.template.configure',
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

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/games/{gameSlug}/puzzles/{templateSlug}/{instanceCode}/manage', name: 'app.games.puzzles.instance.manage')]
    public function managePuzzleInstance(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        DynamicPuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {

        if ($puzzleTemplate->isStatic()) {
            return $this->redirectToRoute($puzzleTemplate->getStaticEditRoute(), [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode()
            ]);
        }

        $dto = DynamicPuzzleConfigDto::makeFromTemplateDefinition($puzzleTemplate);
        $dto->populateFromPuzzleInstance($puzzleInstance);

        $form = $this->generateTemplateForm($puzzleTemplate, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $puzzleInstance->setConfig($dto->toArray());
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
            if ($puzzleInstance instanceof DynamicPuzzleInstanceInterface) {
                $pageVars = [
                    'pageTitle' => sprintf("Play puzzle %s", $puzzleInstance->getName()),
                    'templateName' => $puzzleTemplate->getSlug(),
                    'config' => $this->serializer->serialize($puzzleInstance->getConfig(), 'json'),
                ];
                return $this->render('/visitor/puzzleInstances/dynamic/play.html.twig', $this->populatePageVars($pageVars, $request));
            }
            throw new MismappedPuzzleTemplateException("Puzzle instance is not static, but does not implement DynamicPuzzleInstanceInterface");
        }
    }

    /**
     * @throws \Exception
     */
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/puzzles/templates/{templateSlug}/add-to-game/{gameSlug}/configure', name: 'app.puzzles.template.configure')]
    public function configurePuzzle(
        string $templateSlug,
        Game $game,
        Request $request
    ): Response
    {
        $template = $this->puzzleService->getTemplateBySlug($templateSlug);
        if (null === $template) {
            throw new NotFoundHttpException("No such template");
        }

        $dto = DynamicPuzzleConfigDto::makeFromTemplateDefinition($template);
        $session = $request->getSession();
        $sessionValues = $session->get(PuzzleTemplateController::ADD_TO_GAME_SESSION_KEY);
        $optionsDto = $this->serializer->deserialize($sessionValues, AddPuzzleStepOneDto::class, 'json');
        $form = $this->generateTemplateForm($template, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $instance = new PuzzleInstance(
                instanceCode: $this->puzzleService->generatePuzzleSlug(puzzleName: $optionsDto->puzzleName, puzzleClass: PuzzleInstance::class),
                name: $optionsDto->puzzleName,
                game: $game,
                templateSlug: $templateSlug,
                description: "SOME PUZZLE",
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
            }
        }

        $pageVars = [
            'pageTitle' => sprintf("Configure %s puzzle", $template->getTitle()),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/addToGame/configure.html.twig', $this->populatePageVars($pageVars, $request));
    }

    /**
     * @throws \Exception
     */
    private function generateTemplateForm(PuzzleTemplate $template, DynamicPuzzleConfigDto $dto): FormInterface {
        $builder = $this->createFormBuilder();

        foreach ($template->getConfiguration() as $configurationOption) {
            switch ($configurationOption->getType()) {
                case 'text':
                    $builder->add(
                        $configurationOption->getConfigName(),
                        TextareaType::class,
                        [
                            'label' => $configurationOption->getLabel(),
                            'help' => $configurationOption->getHelpText(),
                            'constraints' => [
                                new NotBlank(),
                            ]
                        ]
                    );
                    break;
                case 'dieRoll':
                    $builder->add(
                        $configurationOption->getConfigName(),
                        DieRollType::class,
                        [
                            'label' => $configurationOption->getLabel(),
                            'help' => $configurationOption->getHelpText(),
                            'constraints' => []
                        ]
                    );
                    break;
                case 'stringArray':
                    $builder->add(
                        $configurationOption->getConfigName(),
                        CollectionType::class,
                        [
                            'entry_type' => TextType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'delete_empty' => true,
                            'prototype' => true,
                            'prototype_options' => [
                                'required' => false,
                                'label' => 'Add an Entry',
                            ],
                            'entry_options' => [
                                'constraints' => [
                                    new NotBlank(),
                                ]
                            ]
                        ]
                    );
                    break;
                default:
                    throw new \Exception(sprintf("Unprocessable option type %s", $configurationOption->getType()));
            }

        }
        $builder->add('submit', SubmitType::class);
        $builder->setData($dto);

        return $builder->getForm();
    }
}
