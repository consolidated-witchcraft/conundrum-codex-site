<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\PuzzleInstance;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleTemplateController;
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
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\PuzzleInstance;
use App\Services\PuzzleTemplate\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
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
                    'templateSlug' => $puzzleTemplate->getSlug(),
                    'instanceCode' => $puzzleInstance->getInstanceCode(),
                    'elementName' => $puzzleTemplate->getElementName(),
                ];
                return $this->render('/visitor/puzzleInstances/dynamic/play.html.twig', $this->populatePageVars($pageVars, $request));
            }
            throw new MismappedPuzzleTemplateException("Puzzle instance is not static, but does not implement DynamicPuzzleInstanceInterface");
        }
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
