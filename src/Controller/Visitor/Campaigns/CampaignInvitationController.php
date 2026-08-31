<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Campaigns;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Campaign\Invitations\InvitePlayerDto;
use App\Dto\Visitor\Campaign\Invitations\RedeemInvitationDto;
use App\Form\Visitor\Campaign\Invitations\InvitePlayerType;
use App\Form\Visitor\Campaign\Invitations\RedeemInvitationType;
use App\Form\Visitor\Campaign\Invitations\AcceptInvitationType;
use App\Form\Visitor\Campaign\Invitations\RevokeInvitationType;
use App\Security\CampaignInvitationVoter;
use App\Security\CampaignManagerVoter;
use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\Campaign\Service\Interfaces\CampaignServiceInterface;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\PuzzleInstance\Infrastructure\CodeGenerator;
use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CampaignInvitationController extends AbstractBaseController
{
    public function __construct(
        private readonly CampaignServiceInterface $campaignService,
        private readonly UserServiceInterface $userService,
        private readonly MailerInterface $mailer,
        #[Autowire('%app.mail.sender%')]
        private readonly string $senderEmail,
        #[Autowire('%app.mail.name%')]
        private readonly string $senderName,
        #[Autowire('%app.siteurl%')]
        private readonly string $siteUrl,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct(featureSwitcherService: $featureSwitcherService);
    }

    /** @throws RandomException */
    #[IsGranted(CampaignManagerVoter::MANAGE, 'campaign')]
    #[Route('/campaigns/{campaignSlug}/invitations/new', name: 'app.campaigns.invite')]
    public function invite(Campaign $campaign, Request $request): Response
    {
        $dto = new InvitePlayerDto();
        $dto->invitationMessage = 'Hi, I would like you to join my campaign on conundrumcodex.com';
        $form = $this->createForm(type: InvitePlayerType::class, data: $dto);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $this->userService->findByEmail(email: (string) $dto->email);
            $invitation = new CampaignInvitation(
                invitationCode: CodeGenerator::generateRandomCode(length: 8),
                email: (string) $dto->email,
                invitationMessage: (string) $dto->invitationMessage,
                campaign: $campaign,
                expiresAt: new \DateTimeImmutable('+24 hours'),
                user: $existingUser instanceof User ? $existingUser : null,
            );
            $this->campaignService->saveInvitation(invitation: $invitation);

            if (!$existingUser instanceof User) {
                try {
                    $this->sendInvitationEmail(invitation: $invitation);
                } catch (TransportExceptionInterface) {
                    $this->addFlash(type: 'danger', message: 'The invitation was created, but the e-mail could not be sent.');
                }
            }

            $this->addFlash(type: 'success', message: 'Campaign invitation sent.');
            return $this->redirectToRoute(route: 'app.user.account', parameters: ['_fragment' => 'invitations']);
        }

        return $this->render(view: 'visitor/campaigns/invitations/invite.html.twig', parameters: $this->populatePageVars(
            pageVars: ['pageTitle' => sprintf('Invite player to %s', $campaign->getName()), 'campaign' => $campaign, 'form' => $form],
            request: $request,
        ));
    }

    #[Route('/campaigns/invitations/redeem', name: 'app.campaigns.invite.redeem')]
    public function redeem(Request $request): Response
    {
        $dto = new RedeemInvitationDto();
        $user = $this->getUser();
        if ($user instanceof User) { $dto->email = $user->getEmail(); }
        $form = $this->createForm(type: RedeemInvitationType::class, data: $dto);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user instanceof User && $user->getEmail() !== $dto->email) {
                throw $this->createAccessDeniedException(message: 'You may only redeem your own invitation.');
            }
            $invitation = $this->campaignService->findInvitationByCodeAndEmail(code: (string) $dto->invitationCode, email: (string) $dto->email);
            if (null === $invitation) {
                $this->addFlash(type: 'error', message: 'No current campaign invitation matches those details.');
            } elseif (!$user instanceof User) {
                return $this->redirectToRoute(route: 'app.auth.register', parameters: ['emailAddress' => $dto->email, 'invitationCode' => $dto->invitationCode]);
            } else {
                $this->campaignService->redeemInvitation(invitation: $invitation, user: $user);
                $this->addFlash(type: 'success', message: 'Invitation accepted. You have joined the campaign.');
                return $this->redirectToRoute(route: 'app.user.account');
            }
        }

        return $this->render(view: 'visitor/campaigns/invitations/redeem.html.twig', parameters: $this->populatePageVars(
            pageVars: ['pageTitle' => 'Join a campaign', 'form' => $form], request: $request,
        ));
    }

    #[IsGranted(CampaignInvitationVoter::ACCEPT, 'invitation')]
    #[Route('/campaigns/invitations/{invitationCode}/accept', name: 'app.campaigns.invite.accept')]
    public function accept(CampaignInvitation $invitation, Request $request): Response
    {
        $form = $this->createForm(type: AcceptInvitationType::class);
        $form->handleRequest(request: $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) { throw $this->createAccessDeniedException(); }
            $this->campaignService->redeemInvitation(invitation: $invitation, user: $user);
            $this->addFlash(type: 'success', message: 'Invitation accepted. You have joined the campaign.');
            return $this->redirectToRoute(route: 'app.user.account');
        }

        return $this->render(view: 'visitor/campaigns/invitations/respond.html.twig', parameters: $this->populatePageVars(
            pageVars: ['pageTitle' => 'Accept campaign invitation', 'invitation' => $invitation, 'form' => $form], request: $request,
        ));
    }

    #[Route('/campaigns/invitations/{invitationCode}/decline', name: 'app.campaigns.invite.decline')]
    public function decline(CampaignInvitation $invitation, Request $request): Response
    {
        $user = $this->getUser();
        if ($user instanceof User && $user->getEmail() !== $invitation->getEmail()) {
            throw $this->createAccessDeniedException(message: 'You may only decline your own invitation.');
        }

        return $this->revokeWithForm(invitation: $invitation, request: $request, successMessage: 'Campaign invitation declined.');
    }

    #[IsGranted(CampaignInvitationVoter::REVOKE, 'invitation')]
    #[Route('/campaigns/{campaignSlug}/invitations/{invitationCode}/revoke', name: 'app.campaigns.invite.revoke')]
    public function revoke(CampaignInvitation $invitation, Request $request): Response
    {
        return $this->revokeWithForm(invitation: $invitation, request: $request, successMessage: 'Campaign invitation revoked.');
    }

    private function revokeWithForm(CampaignInvitation $invitation, Request $request, string $successMessage): Response
    {
        $form = $this->createForm(type: RevokeInvitationType::class);
        $form->handleRequest(request: $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->revoke();
            $this->campaignService->saveInvitation(invitation: $invitation);
            $this->addFlash(type: 'success', message: $successMessage);
            return $this->redirectToRoute(route: 'app.user.account');
        }
        return $this->render(view: 'visitor/campaigns/invitations/respond.html.twig', parameters: $this->populatePageVars(
            pageVars: ['pageTitle' => $successMessage, 'invitation' => $invitation, 'form' => $form], request: $request,
        ));
    }

    /** @throws TransportExceptionInterface */
    private function sendInvitationEmail(CampaignInvitation $invitation): void
    {
        $gamesMaster = $invitation->getCampaign()->getGamesMaster();
        $email = (new TemplatedEmail())
            ->from(new Address(address: $this->senderEmail, name: $this->senderName))
            ->to($invitation->getEmail())
            ->subject(sprintf('Campaign invitation from %s', $gamesMaster?->getEmail() ?? 'Conundrum Codex'))
            ->htmlTemplate('mail/campaign-invitation.html.twig')
            ->context([
                'invitation' => $invitation,
                'registrationLink' => sprintf('%s%s', $this->siteUrl, $this->generateUrl(route: 'app.auth.register', parameters: ['emailAddress' => $invitation->getEmail(), 'invitationCode' => $invitation->getInvitationCode()])),
                'declineLink' => sprintf('%s%s', $this->siteUrl, $this->generateUrl(route: 'app.campaigns.invite.decline', parameters: ['invitationCode' => $invitation->getInvitationCode()])),
            ]);
        $this->mailer->send(message: $email);
    }
}
