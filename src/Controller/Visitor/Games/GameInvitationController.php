<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Games;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Game\Invitations\DeclineInvitationDto;
use App\Dto\Visitor\Game\Invitations\InvitationRedemptionDto;
use App\Dto\Visitor\Game\Invitations\InvitePlayerDto;
use App\Form\Visitor\Game\Invitations\DeclineInvitationType;
use App\Form\Visitor\Game\Invitations\InvitePlayerType;
use App\Form\Visitor\Game\Invitations\LoggedInUserInvitationAcceptanceType;
use App\Form\Visitor\Game\Invitations\RedeemInvitationType;
use App\Form\Visitor\Game\Invitations\RevokeInvitationType;
use App\Security\GameManagerVoter;
use App\Security\InvitationOwnerVoter;
use App\Security\UserOwnsInvitationVoter;
use App\Services\Abuse\Domain\AbuseReport;
use App\Services\Abuse\Service\AbuseReportService;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\PuzzleInstance\Infrastructure\CodeGenerator;
use App\Services\User\Domain\User;
use App\Services\User\Infrastructure\Repository\UserRepository;
use App\Services\User\Service\UserService;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GameInvitationController extends AbstractBaseController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepository $userRepository,
        private readonly GameEntityServiceInterface $gameService,
        private readonly UserService $userService,
        private readonly AbuseReportService $abuseReportService,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct($featureSwitcherService);
    }

    /**
     * @throws RandomException
     */
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/manage/invitations', name: 'app.games.invite')]
    public function invite(
        Game $game,
        Request $request
    ) {
        $dto = new InvitePlayerDto();
        $dto->game = $game;
        $dto->invitationCode = CodeGenerator::generateRandomCode(8);
        $dto->invitationText = 'Hi, I\'d like you to join my game on conundrumcodex.com';
        $form = $this->createForm(InvitePlayerType::class, $dto );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $dto->email;
            $expiration = new \DateTime();
            $expiration->modify('+24 hours');
            $existingUser = $this->userRepository->findOneBy(['email' => $email]);
            $invitation = new GameInvitation(
                invitationCode: $dto->invitationCode,
                email: $email,
                invitationMessage: $dto->invitationText,
                game: $game,
                expiresAt: \DateTimeImmutable::createFromMutable($expiration),
            );
            if ($existingUser) {
                $invitation->setUser($existingUser);
            }
            $this->gameService->saveInvitation($invitation);

            $senderEmail = $this->getParameter('app.mail.sender');
            $senderName = $this->getParameter('app.mail.name');
            if (null === $existingUser) {
                $email = (new TemplatedEmail())
                    ->from(new Address($senderEmail, $senderName))
                    ->to($dto->email)
                    ->subject(sprintf('Invitation from %s', $game->getGamesMaster()->getEmail()))
                    ->htmlTemplate('mail/invitation.html.twig')
                    ->context([
                        'invitation' => $invitation,
                        'registrationLink' => sprintf(
                            "%s%s",
                            $this->getParameter('app.siteurl'),
                            $this->generateUrl('app.auth.register', [
                                'emailAddress' => $dto->email,
                                'invitationCode' => $dto->invitationCode,
                            ])
                        ),
                        'declineLink' => sprintf(
                            '%s%s',
                            $this->getParameter('app.siteurl'),
                            $this->generateUrl('app.games.invite.decline', [
                                'invitationCode' => $dto->invitationCode,
                            ])
                        )
                    ]);
                try {
                    $this->mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', $e->getMessage());
                }

            }
            $this->addFlash('success', 'Invitation Sent');
            return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);

        }
        $pageVars = [
            'pageTitle' => sprintf('Invite player to %s', $game->getName()),
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => sprintf('Manage %s', $game->getName()),
                    'active' => false
                ],
                [
                    'label' => 'Invite Players',
                    'active' => true
                ],
            ],
            'game' => $game,
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/invite.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(InvitationOwnerVoter::MANAGE_INVITATION_ACTION, 'invitation')]
    #[Route('games/{gameSlug}/invitations/{invitationCode}/revoke', name: 'app.games.invite.revoke')]
    public function revoke(
        Request $request,
        GameInvitation $invitation
    ) {
        $form = $this->createForm(RevokeInvitationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->revoke();
            $this->gameService->saveInvitation($invitation);
            $this->addFlash('success', 'Invitation Revoked');
            return $this->redirectToRoute('app.games.manage', ['slug' => $invitation->getGame()->getSlug()]);
        }

        $pageVars = [
            'pageTitle' => sprintf('Really revoke %s\'s  invitation?', $invitation->getEmail()),
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/revoke.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('games/invitations/{invitationCode}/decline', name: 'app.games.invite.decline')]
    public function decline(GameInvitation $invitation, Request $request) {
        $user = $this->getUser();

        if ($user instanceof User && $user->getUserIdentifier() !== $invitation->getEmail()) {
            throw new AccessDeniedHttpException("You may not decline invitations on someone else\'s behalf.");
        }
        $dto = new DeclineInvitationDto($invitation);
        $form = $this->createForm(DeclineInvitationType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $invitation->revoke();
            $this->gameService->saveInvitation($invitation);
            if ($dto->reason !== 'received_in_error') {
                $abuseReport = new AbuseReport(
                    reportedUser: $invitation->getGame()->getGamesMaster(),
                    reason: $dto->reason,
                    notes: $dto->notes,
                    reportingUser: $user ?? null,
                );
                $this->abuseReportService->saveReport($abuseReport);
            }
            $this->addFlash('success', 'Invitation declined');
            return $this->redirectToRoute('app.user.account');
        }


        $pageVars = [
            'pageTitle' => 'Decline Invitation',
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/decline.html.twig', $this->populatePageVars($pageVars, $request));

    }

    #[Route('games/invitations/redeem', name: 'app.games.invite.redeem')]
    public function redeem(
        Request $request
    ) {
        $dto = new InvitationRedemptionDto();
        $form = $this->createForm(RedeemInvitationType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $invitation = $this->gameService->findInvitationByCodeAndEmailAddress($dto->invitationCode, $dto->emailAddress);
            if (!$invitation) {
                $this->addFlash('error', 'Sorry, we couldn\'t find a current Game Invitation matching these details. Please check and try again' );
            } else {
                $user = $invitation->getUser();
                if (null === $user) {
                    $this->addFlash('success', 'Invitation found. Please register to continue: we\'ve filled in some details for you!');
                    return $this->redirectToRoute('app.auth.register', [
                        'emailAddress' => $invitation->getEmail(),
                        'invitationCode' => $invitation->getInvitationCode()
                    ]);
                }
                $invitation->markUsed();
                $this->gameService->saveInvitation($invitation);
            }

            return $this->redirectToRoute('app.games.play', ['gameSlug' => $invitation->getGame()->getSlug()]);
        }

        $pageVars = [
            'pageTitle' => 'Redeem your Invitation',
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/redeem.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('games/invitations/{invitationCode}/accept', name: 'app.games.invite.accept')]
    #[IsGranted(UserOwnsInvitationVoter::REDEEM_INVITATION, 'invitation')]
    public function accept(
        GameInvitation $invitation,
        Request $request
    ): Response
    {
        $form = $this->createForm(LoggedInUserInvitationAcceptanceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $this->userService->redeemInvitationForUser($invitation, $user);
            }
            $this->addFlash('success', 'Invitation accepted! Enjoy your game!');
            return $this->redirectToRoute('app.games.index');
        }
        $pageVars = [
            'pageTitle' => 'Redeem your Invitation',
            'invitation' => $invitation,
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/redeem.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
