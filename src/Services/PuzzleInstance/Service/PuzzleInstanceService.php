<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceProviderInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceProviderInterface;
use App\Services\PuzzleInstance\Infrastructure\PuzzleInstanceRepository;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Exceptions\TemplateNotFoundException;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateRegistryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class PuzzleInstanceService extends AbstractDomainEntityService implements PuzzleInstanceEntityServiceInterface
{

    /**
     * @var array<StaticPuzzleInstanceProviderInterface>
     */
    private array $staticInstanceProviders = [];


    /**
     * @param EntityManagerInterface $entityManager
     * @param PuzzleTemplateRegistryInterface $puzzleTemplateRegistry
     * @param PuzzleInstanceRepository $puzzleInstanceRepository
     * @param iterable<StaticPuzzleInstanceProviderInterface> $staticInstanceProviders
     */
    public function __construct(
        private readonly PuzzleTemplateRegistryInterface $puzzleTemplateRegistry,
        private readonly PuzzleInstanceRepository $puzzleInstanceRepository,
        EntityManagerInterface $entityManager,
        #[AutowireIterator('app.static_puzzle_provider')]
        iterable $staticInstanceProviders
    ) {
        foreach ($staticInstanceProviders as $provider) {
            $this->staticInstanceProviders[$provider->providesTemplateInstances()] = $provider;
        }
        parent::__construct($entityManager);
    }

    /**
     * @throws TemplateNotFoundException
     */
    public function getInstanceByTemplateAndCode(string $templateSlug, string $instanceCode): ?PuzzleInstanceInterface
    {

        $template = $this->puzzleTemplateRegistry->getTemplate($templateSlug);
        if (!$template) {
            throw new TemplateNotFoundException(sprintf("No template found %s", $templateSlug));
        }
        if (isset($this->staticInstanceProviders[$templateSlug])) {
            return $this->staticInstanceProviders[$templateSlug]->getInstance($instanceCode);
        }

        $instance = $this->puzzleInstanceRepository->findOneBy(['instanceCode' => $instanceCode]);

        if ($instance) {
            $instance->setTemplate($template);
            return $instance;
        }
        return null;
    }

    public function saveInstance(PuzzleInstanceInterface $instance): void
    {
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }

    public function deleteInstance(PuzzleInstanceInterface $instance): void
    {
        $this->entityManager->remove($instance);
        $this->entityManager->flush();
    }

    public function generateNewInstanceCode(PuzzleTemplate $puzzleTemplate): string
    {
            return $this->getProviderForTemplate($puzzleTemplate)->generateNewInstanceCode();
    }

    private function getProviderForTemplate(PuzzleTemplate $puzzleTemplate): PuzzleInstanceProviderInterface {
        if ($puzzleTemplate->isStatic()) {
            foreach ($this->staticInstanceProviders as $provider) {
                if ($provider->providesTemplateInstances() === $puzzleTemplate->getSlug()) {
                    return $provider;
                }
            }
        } else {
            return $this->puzzleInstanceRepository;
        }

        throw new \LogicException(sprintf('No puzzle-instance provider is registered for template "%s".', $puzzleTemplate->getSlug()));
    }
}
