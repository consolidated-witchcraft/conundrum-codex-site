<?php

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\User\RegisterUserDto;
use App\Form\Visitor\LoginType;
use App\Form\Visitor\RegistrationForm;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractBaseController
{

    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly UserServiceInterface $userService,
        private readonly GameServiceInterface $gameService
    ) {
    }

    #[Route('/register', name: 'app.auth.register')]
    public function register(
        Request $request,
        Recaptcha3Validator $recaptcha3Validator,
        #[MapQueryParameter] ?string $emailAddress = null,
        #[MapQueryParameter] ?string $invitationCode = null,
        #[MapQueryParameter] ?string $redirectRoute = null,
    ): Response{

        $userDto = new RegisterUserDto();
        if (null !== $emailAddress) {
            $userDto->emailAddress = $emailAddress;
        }
        if (null !== $invitationCode) {
            $userDto->invitationCode = $invitationCode;
        }
        $form = $this->createForm(RegistrationForm::class, $userDto);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $accountType = $this->userService->getAccountTypeByHandle('free');
            $user = new User(
                email: $userDto->emailAddress,
                username: $userDto->userName,
                userAccountType: $accountType,
                password: '',
                hasAcceptedCookies: $userDto->acceptCookies,
                profilePublic: $userDto->profilePublic,

            );

            $user->setPassword($this->userService->hashPassword($user, $plainPassword));

            $success = true;
            $violations = $this->userService->validateUser($user);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }


            if ($success === true && null !== $userDto->invitationCode) {
                $invitation = $this->gameService->findInvitationByCodeAndEmailAddress(invitationCode: $userDto->invitationCode, emailAddress: $userDto->emailAddress);
                if (null === $invitation) {
                    $success = false;
                    $this->addFlash('error', 'We couldn\'t find an invitation matching those details. Please check and try again.');
                }
            } else {
                $invitation = null;
            }


            if ($success) {
                $this->userService->saveUser($user);

                $hasInvitation = false;
                if (null !== $invitation) {
                    $hasInvitation = true;
                    $this->userService->redeemInvitationForUser($invitation, $user);
                }


                $this->addFlash('success', $hasInvitation ? 'Your account has been created, and you\'ve been added to your game!' : 'Your account has been created. You may now log in.');
                return $this->redirectToRoute('app.auth.login');
            }

        }
        $pageVars = [
            'pageTitle' => 'Log In or Register',
            'registrationForm' => $form
        ];

        return $this->render('/visitor/registration/register.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/login', name: 'app.auth.login', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);

        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $pageVars = [
            'pageTitle' => 'Login',
            'form' => $form,
            'error' => $error,
            'last_username' => $lastUsername
        ];

        return $this->render('/visitor/security/login.html.twig', $this->populatePageVars(request: $request, pageVars: $pageVars));
    }

    #[Route(path: '/logout', name: 'app.auth.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
