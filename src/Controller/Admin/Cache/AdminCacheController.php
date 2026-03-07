<?php

declare(strict_types=1);

namespace App\Controller\Admin\Cache;

use App\Controller\AbstractBaseController;
use App\Form\Admin\AdminCacheClearType;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
class AdminCacheController extends AbstractBaseController {

    public function __construct(
        #[Autowire(service: 'cache_clearer')]
        private readonly CacheClearerInterface $cacheClearer,

        #[Autowire('%kernel.cache_dir%')] private readonly string $cacheDirectory,
        FeatureSwitcherServiceInterface $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[IsGranted('manage_cache')]
    #[Route('admin/cache', name: 'admin.cache.manage')]
    public function manageCache(Request $request): Response {
        $form = $this->createForm(AdminCacheClearType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->cacheClearer->clear($this->cacheDirectory);
            $this->addFlash('success', 'Cache cleared.');

        }
        $pageVars = [
            'pageTitle' => 'Manage the Cache',
            'form' => $form,
        ];
        return $this->render('admin/cache/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
