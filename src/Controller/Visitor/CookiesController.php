<?php

declare(strict_types=1);

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CookiesController extends AbstractBaseController
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        FeatureSwitcherServiceInterface $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('/cookies/accept', name: 'app.cookies.accept', methods: ['POST'])]
    public function acceptCookies(Request $request): Response {
        $session = $request->getSession();
        $session->set(self::ACCEPT_COOKIES_SESSION_KEY, true);
        $user = $this->getUser();
        if ($user !== null && $user instanceof User) {
            $user->setHasAcceptedCookies(true);
            $this->userService->saveUser($user);
        }
        $route = $request->headers->get('referer');
        $this->addFlash('success', 'Your preferences have been saved.');
        return $this->redirect($route);
    }
}
