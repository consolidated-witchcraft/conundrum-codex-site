<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Controller\AbstractBaseController;
use App\Controller\Traits\GeneratesPagerTrait;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Admin\User\AdminUserDto;
use App\Dto\Admin\User\AdminUserGameDto;
use App\Dto\Visitor\User\UserBlockDto;
use App\Form\Admin\Users\AdminBlockUserType;
use App\Form\Admin\Users\AdminDeleteUserType;
use App\Form\Admin\Users\AdminGameType;
use App\Form\Admin\Users\AdminUnblockUserType;
use App\Form\Admin\Users\AdminUserEditType;
use App\Form\Admin\Users\AdminUserSearchType;
use App\Services\Abuse\Service\AbuseReportService;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserBlock;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminUserController extends AbstractBaseController
{

    use HandlesFileUploadsTrait;
    use GeneratesPagerTrait;

    public function __construct(
        private readonly UserServiceInterface                                                    $userService,
        private readonly UserPasswordHasherInterface                                             $userPasswordHasher,
        private readonly ValidatorInterface                                                      $validator,
        private readonly EntityManagerInterface                                                  $entityManager,
        private readonly PuzzleInstanceEntityServiceInterface                                    $puzzleInstanceService,
        private readonly AbuseReportService                                                      $abuseReportService,
        protected readonly SluggerInterface                                                      $slugger,
        private readonly GameEntityServiceInterface                                              $gameService,
        private readonly ManagedFileServiceInterface                                             $filesService,
        private readonly ManagedImageServiceInterface                                            $managedImageService,
        FeatureSwitcherServiceInterface                                                          $featureSwitcherService,
        #[Autowire('%kernel.project_dir%/public/uploads/images/avatar')] private readonly string $publicImagesDirectory

    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('admin/users', name: 'admin.users.manage')]
    #[IsGranted('manage_users')]
    public function index(
        Request $request,
        #[MapQueryParameter] ?int $page = 1,
        #[MapQueryParameter] ?int $resultsPerPage = 10,
    ): Response {
        $form = $this->createForm(AdminUserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $results = $this->userService->findByEmailOrUserName(searchTerms: $data['search'], firstResult: ($resultsPerPage * ($page - 1)),maxResults: $resultsPerPage);
        } else {
            $results = $this->userService->getUsersPaginated(firstResult: ($resultsPerPage * ($page-1)), maxResults: $resultsPerPage);
        }

        $pageVars = [
            'pageTitle' => 'Manage Users',
            'form' => $form,
            'resultsPerPage' => $resultsPerPage,
            'pager' => $this->generatePager($page, $resultsPerPage, $this->userService->getUsersCount()),
            'currentPage' => $page,
            'users' => $results,
        ];
        return $this->render('admin/users/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{userId}/edit', name: 'admin.users.edit')]
    #[IsGranted('manage_users')]
    public function edit(User $user, Request $request): Response {
        $currentUser = $this->getUser();

        $selfEditMode = $user === $currentUser;

        if ($selfEditMode) {
            $this->addFlash("warning", "You are editing your own user. Password change requests and e-mail changes will be ignored.");
        }
        $dto = AdminUserDto::makeFromUser($user);
        $form = $this->createForm(AdminUserEditType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('avatar')->getData();
            if ($imageFile) {
                $avatar = $this->handleImageUpload($imageFile, $this->publicImagesDirectory, $user->getAvatar());
                $user->setAvatar($avatar);
            }
            if (!$selfEditMode) {
                $user->setEmail($dto->email);
            }
            $user->setHasAcceptedCookies($dto->acceptedCookies);
            $user->setIsProfilePublic($dto->profilePublic);
            $user->setUserAccountType($dto->userAccountType);
            $user->setUsername($dto->username);
            $user->setRoles($dto->roles);
            $user->setFeats($dto->feats);

            if (null !== $dto->plainPassword && !$selfEditMode) {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->plainPassword));
            }
            $success = true;
            $violations = $this->userService->validateUser($user);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }

            if ($success) {
                $this->userService->saveUser($user);
                $this->addFlash('success', 'User changes saved successfully.');
                return $this->redirectToRoute('admin.users.manage');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf('Edit user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{userId}/games', name: 'admin.users.games')]
    #[IsGranted('manage_user_games')]
    public function manageGames(User $user, Request $request): Response {
        $currentUser = $this->getUser();

        $selfEditMode = $user === $currentUser;

        if ($selfEditMode) {
            $this->addFlash("warning", "You are editing your own user. Password change requests and e-mail changes will be ignored.");
        }
        $dto = AdminUserGameDto::makeFromUser($user);
        $form = $this->createForm(AdminGameType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($dto->games as $gameId) {
                $game = $this->gameService->find($gameId);
                $game->addPlayer($user);
                $this->gameService->saveGame($game);
            }
            $this->addFlash('success', 'Games saved successfully.');
            return $this->redirectToRoute('admin.users.manage');
        }
        $pageVars = [
            'pageTitle' => sprintf('Assign games to user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/manage-games.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{userId}/block', name: 'admin.users.block')]
    #[IsGranted('block_users')]
    public function blockUser(User $user, Request $request): Response {
        $currentUser = $this->getUser();
        if ($user === $currentUser) {
            $this->addFlash('error', 'You can\'t block yourself!');
            return $this->redirectToRoute('admin.users.manage');
        }

        $blockDto = new UserBlockDto($user);
        $form = $this->createForm(AdminBlockUserType::class, $blockDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $userBlock = new UserBlock(
                $user,
                $blockDto->reason,
                $blockDto->expiresAt !== null ? \DateTimeImmutable::createFromInterface($blockDto->expiresAt) : null
            );
            $success = true;
            $violations = $this->validator->validate($userBlock);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($userBlock);
                $this->entityManager->flush();
                $this->addFlash('success', 'User blocked successfully.');
                return $this->redirectToRoute('admin.users.manage');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf('Block user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/block.html.twig', $this->populatePageVars($pageVars, $request));

    }

    #[Route('admin/users/{userId}/unblock', name: 'admin.users.unblock')]
    #[IsGranted('block_users')]
    public function unblockUser(User $user, Request $request): Response {


        $blockDto = new UserBlockDto($user);
        $form = $this->createForm(AdminUnblockUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userBlock = $user->getUserBlock();


            $this->entityManager->remove($userBlock);
            $this->entityManager->flush();
            $this->addFlash('success', 'User unblocked successfully.');
            return $this->redirectToRoute('admin.users.manage');

        }
        $pageVars = [
            'pageTitle' => sprintf('Unblock user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/block.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{userId}/delete', name: 'admin.users.delete')]
    #[IsGranted('manage_users')]
    public function deleteUser(User $user, Request $request): Response {
        $form = $this->createForm(AdminDeleteUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            //Delete all invitations
            foreach ($user->getGames() as $game) {
                foreach ($game->getGameInvitations() as $gameInvitation) {
                    if ($gameInvitation->getUser() === $user) {
                        $this->entityManager->remove($gameInvitation);
                    }
                }
            }

            //Delete All Games and static puzzles.
            foreach ($user->getGamesMastered(true) as $game) {
               $staticPuzzleInstances = $this->puzzleInstanceService->getStaticPuzzleInstancesForGame($game);
               foreach ($staticPuzzleInstances as $staticPuzzleInstance) {
                   $this->puzzleInstanceService->deleteInstance($staticPuzzleInstance);
               }
               $this->entityManager->remove($game);
            }
            $abuseReports = $this->abuseReportService->getAbuseReportsByUser($user);

            foreach ($abuseReports as $report) {
                $this->abuseReportService->deleteReport($report);
            }

            $reportsConcerningUser = $this->abuseReportService->getAbuseReportsForReportedUser(user: $user, checkedOnly: false);

            foreach ($reportsConcerningUser as $report) {
                $this->abuseReportService->deleteReport($report);
            }

            $passwordResets = $this->userService->getPasswordResetRequestsForUser($user);
            foreach ($passwordResets as $reset) {
                $this->entityManager->remove($reset);
            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
            return $this->redirectToRoute('admin.users.manage');

        }
        $pageVars = [
            'pageTitle' => sprintf('Delete user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/delete.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
