<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Account;

use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Visitor\User\UserChangePasswordDto;
use App\Dto\Visitor\User\UserEditDto;
use App\Form\Visitor\Account\ChangePasswordType;
use App\Form\Visitor\Account\UserEditType;
use App\Services\Files\Service\Interfaces\FilesEntityServiceInterface;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AccountController extends AbstractBaseController
{
    use HandlesFileUploadsTrait;

    public function __construct(
        private readonly GameEntityServiceInterface                                              $gameService,
        private readonly EntityManagerInterface                                                  $entityManager,
        private readonly ValidatorInterface                                                      $validator,
        private readonly UserPasswordHasherInterface                                             $passwordHasher,
        protected readonly SluggerInterface                                                      $slugger,
        protected readonly FilesEntityServiceInterface                                           $filesService,
        #[Autowire('%kernel.project_dir%/public/uploads/images/avatar')] private readonly string $publicImagesDirectory,
    ) {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'app.user.account')]
    public function index(Request $request): Response
    {

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $pageVars = [
            'pageTitle' => 'Account',
            'user' => $user,
            'invitations' => $this->gameService->getOutstandingInvitationsForUser($user)
        ];
        return $this->render('/visitor/account/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account/change-details', name: 'app.user.account.edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $dto = UserEditDto::makeFromUser($user);
        $form = $this->createForm(UserEditType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('avatar')->getData();
            if ($imageFile) {
                $avatar = $this->handleFileUpload($imageFile, $this->publicImagesDirectory);
                $user->setAvatar($avatar);
            }
            $user->setEmail($dto->emailAddress);
            $user->setUsername($dto->userName);

            $user->getUserPreferences()->setPreferredFontStack($dto->userPreferences->preferredFontStack);
            $user->getUserPreferences()->setHasAcceptedCookies($dto->userPreferences->hasAcceptedCookies);
            $user->getUserPreferences()->setProfilePublic($dto->userPreferences->profilePublic);


            $success = true;
            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $success = false;
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Your account has been edited.');
                return $this->redirectToRoute('app.user.account');
            }
        }
        $pageVars = [
            'pageTitle' => 'Change my Details',
            'user' => $user,
            'form' => $form,
        ];
        return $this->render('/visitor/account/change-details.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account/change-password', name: 'app.user.account.change-password')]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $dto = new UserChangePasswordDto();
        $form = $this->createForm(ChangePasswordType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->newPassword));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your password has been changed.');
            return $this->redirectToRoute('app.user.account');
        }
        $pageVars = [
            'pageTitle' => 'Change my Password',
            'form' => $form,
        ];
        return $this->render('/visitor/account/change-password.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account/{id}/view', name: 'app.user.account.view')]
    public function viewAccount(User $user, Request $request): Response {
        if (!$user->isProfilePublic()) {
            $pageVars = [
                'pageTitle' => 'Private Profile',
            ];
            return $this->render('/visitor/account/view_private.html.twig', $this->populatePageVars($pageVars, $request));
        }

        $pageVars = [
            'pageTitle' => sprintf("%s' Profile", $user->getUsername()),
            'user' => $user
        ];
        return $this->render('/visitor/account/view.html.twig', $this->populatePageVars($pageVars, $request));
    }




}
