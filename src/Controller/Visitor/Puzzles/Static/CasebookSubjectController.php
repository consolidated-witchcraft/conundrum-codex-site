<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\Static;

use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectDto;
use App\Form\Mapper\DynamicInstanceDataMapper;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookSubjectType;
use App\Form\Visitor\Puzzle\Static\Casebook\DeleteCasebookSubjectType;
use App\Security\GameManagerVoter;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Files\Service\Interfaces\FilesEntityServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CasebookSubjectController extends AbstractPuzzleController
{
    use HandlesFileUploadsTrait;
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/uploads/images/casebook')] private string $publicImagesDirectory,
        PuzzleTemplateServiceInterface $puzzleTemplateService,
        PuzzleInstanceEntityServiceInterface $puzzleInstanceService,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        ValidatorInterface $validator,
        FilesEntityServiceInterface $filesService,
        DynamicInstanceDataMapper $dynamicInstanceDataMapper,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct(
            puzzleTemplateService: $puzzleTemplateService,
            puzzleInstanceTemplateService: $puzzleInstanceService,
            serializer: $serializer,
            entityManager: $entityManager,
            slugger: $slugger,
            validator: $validator,
            filesService: $filesService,
            dynamicInstanceDataMapper: $dynamicInstanceDataMapper,
            featureSwitcherService: $featureSwitcherService
        );
    }
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/add', name: 'app.puzzles.static.casebook.subjects.add')]
    public function addSubject(
        Game $game,
        PuzzleTemplate $puzzleTemplate,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request
    ): Response {
        if (!$puzzleInstance instanceof  Casebook) {
            throw new BadRequestException();
        }

        $dto = new CasebookSubjectDto(casebook: $puzzleInstance);

        $form = $this->createForm(CasebookSubjectType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            $clues = new ArrayCollection();
            $subject = new CasebookSubject(
                name: $dto->name,
                description: $dto->description,
                casebook: $puzzleInstance,
                casebookSubjectType: $dto->type,
                casebookSubjectClues: $clues,
                casebookSubjectNotes: new ArrayCollection(),
                casebookSubjectImageFile: $imageFile ? $this->handleFileUpload($imageFile, $this->publicImagesDirectory) : null
            );
            if ($dto->revealed) {
                $subject->markRevealed();
            }

            foreach ($dto->clues as $clueEntry) {
                if (!$clueEntry->isBlank()) {
                    $clues->add(new CasebookSubjectClue(
                        title: $clueEntry->title,
                        body: $clueEntry->body,
                        type: $clueEntry->type,
                        casebookSubject: $subject
                    ));
                }


            }

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
            $this->addFlash('success', 'Subject added.');
            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode()
            ]);
        }

        $pageVars = [
            'pageTitle' => sprintf("Add a subject to  '%s'", $puzzleInstance->getName()),
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
                    ])
                ],
                'subject' => [
                    'label' => "Add Subject",
                    'active' => true
                ]
            ],
            'form' => $form,
            'game' => $game,
        ];
        return $this->render('/visitor/puzzleInstances/casebook/subjects/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/edit', name: 'app.puzzles.static.casebook.subjects.edit')]
    public function editSubject(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject,
        CasebookSubjectClueRepository $casebookSubjectClueRepository,
        Request $request
    ): Response {
        if (!$puzzleInstance instanceof  Casebook) {
            throw new BadRequestException();
        }

        $dto = CasebookSubjectDto::makeFromCasebookSubject($subject);
        $form = $this->createForm(CasebookSubjectType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setName($dto->name);
            $subject->setDescription($dto->description);
            $imageFile = $form->get('image')->getData();
            $subject->setCasebookSubjectType($dto->type);

            if ($imageFile) {
                $subject->setCasebookSubjectImageFile($this->handleFileUpload($imageFile, $this->publicImagesDirectory));
            }
            if ($dto->revealed && !$subject->isRevealed()) {
                $subject->markRevealed();
            }
            if (!$dto->revealed && $subject->isRevealed()) {
                $subject->unreveal();
            }
            foreach ($dto->clues as $clueEntry) {
                if ($clueEntry->id !== null) {
                    $existingClue = $casebookSubjectClueRepository->find($clueEntry->id);
                    $existingClue->setTitle($clueEntry->title);
                    $existingClue->setBody($clueEntry->body);
                    $existingClue->setType($clueEntry->type);

                    $this->entityManager->persist($existingClue);
                } else {
                    if (!$clueEntry->isBlank()) {
                        $newClue = new CasebookSubjectClue(
                            title: $clueEntry->title,
                            body: $clueEntry->body,
                            type: $clueEntry->type,
                            casebookSubject: $subject
                        );
                        $this->entityManager->persist($newClue);
                    }

                }
            }
            if ($dto->revealed) {
                $subject->markRevealed();
            }
            $this->entityManager->persist($subject);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf("%s '%s' edited.", (string) $subject->getCasebookSubjectType(), $subject->getName()));
            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode(),
            ]);
        }
        $pageVars =[
            'pageTitle' => sprintf('Edit subject "%s"', $subject->getName()),
            'form' => $form,
            'subject' => $subject,
            'game' => $game,
            'template' => $puzzleTemplate,
            'instance' => $puzzleInstance,
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
                    ])
                ],
                'subject' => [
                    'label' => sprintf("Edit %s", $subject->getName()),
                    'active' => true
                ]
            ],
        ];
        return $this->render('/visitor/puzzleInstances/casebook/subjects/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/delete', name: 'app.puzzles.static.casebook.subjects.delete')]
    public function deleteSubject(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject,
        CasebookSubjectClueRepository $casebookSubjectClueRepository,
        Request $request
    ) : Response {
        $form = $this->createForm(DeleteCasebookSubjectType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->remove($subject);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf("Subject '%s' deleted.", $subject->getName()));
            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode(),
            ]);
        }
        $pageVars = [
            'pageTitle' => sprintf('Delete subject "%s"', $subject->getName()),
            'form' => $form,
        ];
        return $this->render('/visitor/puzzleInstances/casebook/subjects/delete.html.twig', $this->populatePageVars($pageVars, $request));

    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/discover', name: 'app.puzzles.static.casebook.subjects.discover')]
    public function discoverSubject(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject
    ): Response {
        $subject->markRevealed();
        $this->entityManager->persist($subject);
        $this->entityManager->flush();
        $this->addFlash('success', sprintf("%s revealed", $subject->getName()));
        return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
            'gameSlug' => $game->getSlug(),
            'templateSlug' => $puzzleTemplate->getSlug(),
            'instanceCode' => $puzzleInstance->getInstanceCode(),
        ]);
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route(
        path: 'games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/clues/{clueId}/reveal',
        name: 'app.puzzles.static.casebook.subjects.clues.reveal',
        methods: ['POST', 'GET']
    )]
    public function revealClue(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject,
        CasebookSubjectClue $clue,
        Request $request,
    ): Response {
        if (!$puzzleInstance instanceof Casebook) {
            return new JsonResponse([
                'message' => 'Not a Casebook Instance'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        if (null !== $clue->getRevealedDate()) {
            return new JsonResponse([
                'message' => 'Clue already revealed'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $clue->reveal();
        $this->entityManager->persist($clue);
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);
    }

}
