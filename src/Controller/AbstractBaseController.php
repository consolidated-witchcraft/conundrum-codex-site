<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Page\Domain\MetaTag;
use App\Services\Page\Domain\NavItem;
use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{

    protected const string ACCEPT_COOKIES_SESSION_KEY = 'userHasAcceptedCookies';

    final public function populatePageVars(array $pageVars, Request $request): array {
        $route = $request->get('_route');
        $pageVars['siteName'] = 'The Conundrum Codex';
        $user = $this->getUser();
        $pageVars['mainNav'] = $this->buildMainNav($user);
        $pageVars['userNav'] = $this->buildUserNav($user);
        $this->setActiveTrail($pageVars['mainNav'], $route);

        if (!isset($pageVars['breadcrumbs'])) {
            $pageVars['breadcrumbs'] = [];
        }
        $pageVars['pageClass'] = $pageVars['pageClass'] ?? 'page';
        $pageVars['hideBugReportLink'] = $pageVars['hideBugReportLink'] ?? false;
        $pageVars['userCartId'] = $user instanceof User ? $user->getUserCart()->getId() : null;
        $pageVars['showCookiesMessage'] = $this->shouldShowCookieMessage($request, $user);
        return $this->populateMetaTags($pageVars);
    }

    private function shouldShowCookieMessage(Request $request, ?User $user = null): bool {
        if ($this->getUser() && $user instanceof User && $user->getHasAcceptedCookies() === true) {
            return false;
        }
        $session = $request->getSession();
        return !$session->get(self::ACCEPT_COOKIES_SESSION_KEY);
    }

    private function buildGamesItem(User $user): NavItem {
        $gamesItem = new NavItem(
            handle: 'games',
            label: 'My Games',
            route: 'app.games.index'
        );
        foreach ($user->getGames() as $game) {
            $gamesItem->addChild(new NavItem(
                handle: $game->getSlug(),
                label: $game->getName(),
                route: 'app.games.play',
                routeArguments: [
                    'gameSlug' => $game->getSlug(),
                ]
            ));
        }
        foreach ($user->getGamesMastered() as $game) {
            $gamesItem->addChild(new NavItem(
                handle: $game->getSlug(),
                label: $game->getName(),
                route: 'app.games.manage',
                routeArguments: [
                    'gameSlug' => $game->getSlug(),
                ]
            ));
        }
        $gamesItem->addChild(new NavItem(
            handle: 'all_games',
            label: 'All Games',
            route: 'app.games.index',
            routeArguments: []
        ));
        return $gamesItem;
    }

    private function setActiveTrail(array &$nav, string $route): void {
        foreach ($nav as $navItem) {
            if ($navItem->getRoute() === $route) {
                $navItem->setActive(true);
            } else {
                foreach ($navItem->getChildren() as $child) {
                    if ($child->getRoute() === $route) {
                        $child->setActive(true);
                        $navItem->setActiveTrail(true);
                    }
                }
            }
        }
    }

    private function populateMetaTags(array $pageVars): array {
        if (!isset($pageVars['meta'])) {
            $pageVars['meta'] = [];
        }
        if (!isset($pageVars['meta']['description'])) {
            $pageVars['meta']['description'] = new MetaTag(
                name: 'description',
                content: 'The Conundrum Codex is an online site that allows players of all ages and neurodiversity statuses to participate in online TTRPG puzzles.'
            );
        }
        if (!isset($pageVars['meta']['viewport'])) {
            $pageVars['meta']['viewport'] = new MetaTag(
                name: "viewport",
                content: "width=device-width, initial-scale=1"
            );
        }
        return $pageVars;
    }

    private function buildUserNav(? User $user): array {
        $navItems = [];
        if ($user instanceof User) {
            $navItems['account'] = new NavItem(
                handle: 'account',
                label: 'Account',
                route: 'app.user.account'
            );

            if ($user->hasPermissionKey('admin_dash')) {
                $navItems['admin_dash'] =  new NavItem(
                    handle: 'admin_dash',
                    label: 'Admin Dashboard',
                    route: 'admin.dashboard'
                );
            }

            if ($user->hasPermissionKey('create_events')) {
                $navItems['manage_events'] =  new NavItem(
                    handle: 'manage_events',
                    label: 'Manage Events',
                    route: 'events.dashboard'
                );
            }

            $navItems['games'] = new NavItem(
                handle: 'games',
                label: 'My Games',
                route: 'app.games.index'
            );

            $navItems['logout'] = new NavItem(
                handle: 'logout',
                label: 'Log Out',
                route: 'app.auth.logout'
            );
        } else {
            $navItems['login'] = new NavItem(
                handle: 'login',
                label: 'Login',
                route: 'app.auth.login'
            );
            $navItems['register'] = new NavItem(
                handle: 'register',
                label: 'Register',
                route: 'app.auth.register'
            );
        }
        return $navItems;
    }

    private function buildMainNav(?User $user): array {
        $mainNav = [
            new NavItem(
                handle: 'home',
                label: 'Home',
                route: 'app.pages.home'
            ),
            new NavItem(
                handle: 'puzzletemplates',
                label: 'Puzzles & Tools',
                route: 'app.puzzles.template.index'
            ),
            new NavItem(
                handle: 'about',
                label: 'About',
                route: 'app.pages.about'
            ),

            new NavItem(
                handle: 'events',
                label: 'Events',
                route: 'app.events.index'
            )
        ];

        if ($user instanceof User) {
            $mainNav['games'] = $this->buildGamesItem($user);
        }
        return $mainNav;
    }
}
