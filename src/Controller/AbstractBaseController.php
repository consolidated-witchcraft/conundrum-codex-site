<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\FeatureSwitcher\Service\Exceptions\UnknownFeatureHandleException;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Page\Domain\MetaTag;
use App\Services\Page\Domain\NavItem;
use App\Services\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{
    protected const string ACCEPT_COOKIES_SESSION_KEY = 'userHasAcceptedCookies';

    public function __construct(
        protected readonly FeatureSwitcherServiceInterface $featureSwitcherService
    ) {

    }

    final public function populatePageVars(array $pageVars, Request $request): array {
        $route = $request->get('_route');

        $pageVars['siteName'] = 'The Conundrum Codex';
        $user = $this->getUser();
        if ($user instanceof User) {
            $pageVars['preferredFontStack'] = $user->getUserPreferences()->getPreferredFontStack()->getKey();
        } else {
            $pageVars['preferredFontStack'] = 'regular';
        }

        $pageVars['mainNav'] = $this->buildMainNav($user);
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
            label: 'Games',
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

    /**
     * @throws UnknownFeatureHandleException
     */
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
            )
        ];



        if ($user instanceof User) {
            $mainNav['games'] = $this->buildGamesItem($user);
        }



        if ($this->featureSwitcherService->isFeatureEnabled('EVENTS_SYSTEM')) {
            $mainNav['events'] = new NavItem(
                handle: 'events',
                label: 'Events',
                route: 'app.events.index'
            );
        }

        if ($this->featureSwitcherService->isFeatureEnabled('WORLDS_SYSTEM')) {
            $mainNav['worlds'] = new NavItem(
                handle: 'worlds',
                label: 'Worldbooks',
                route: 'app.worlds.index'
            );
        }

        if ($user instanceof User && $user->hasPermissionKey('admin_dash')) {
            $mainNav['admin'] = new NavItem(
                handle: 'admin',
                label: 'Admin Dashboard',
                route: 'admin.dashboard'
            );
        }
        return $mainNav;
    }
}
