<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory;


use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateModuleStructureValidationException;
use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateDefinitionValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateModuleStructureValidator;
use App\Services\PuzzleTemplate\Service\PuzzleTemplateRegistry;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[AutoconfigureTag('kernel.cache_warmer')]
class PuzzleTemplateRegistryFactory implements CacheWarmerInterface
{

    private const string CACHE_KEY = 'puzzleTemplateRegistry';

    private const string PUZZLE_TEMPLATES_DIRECTORY = 'puzzleTemplates';

    private string $modulesDirectoryName;
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly PuzzleTemplateModuleStructureValidator $moduleStructureValidator,
        private readonly PuzzleTemplateDefinitionValidator $definitionValidator,
        private readonly LoggerInterface $logger,
        string $projectRootDirectory
    ) {
        $this->modulesDirectoryName = sprintf("%s/%s", $projectRootDirectory, self::PUZZLE_TEMPLATES_DIRECTORY);
    }
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @throws PuzzleTemplateRegistryBuildException
     */
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->generatePuzzleTemplateRegistry(true);
        return [];
    }

    /**
     * @throws PuzzleTemplateRegistryBuildException
     * @throws InvalidArgumentException
     */
    public function generatePuzzleTemplateRegistry(bool $forceRefresh = false, bool $verbose = false): PuzzleTemplateRegistry {
        if ($forceRefresh) {
            $this->cache->delete(self::CACHE_KEY);
        }
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($verbose) {
            $finder = new Finder();
            $finder->directories()->in($this->modulesDirectoryName);
            if (!$finder->hasResults()) {
                throw new PuzzleTemplateRegistryBuildException("Could not build Template registry: puzzleTemplatesFolder is not populated");
            }
            foreach ($finder as $directory) {
                try {
                    $this->moduleStructureValidator->validate($this->modulesDirectoryName, $directory->getFilename());
                } catch (PuzzleTemplateModuleStructureValidationException $e) {
                    $this->logger->critical($e->getMessage());
                    if ($verbose) {
                        throw new PuzzleTemplateRegistryBuildException($e->getMessage(), $e->getCode(), $e);
                    }
                }

                $templateDefinitionPath = sprintf("%s/%s/%s", $this->modulesDirectoryName, $directory->getFilename(), 'template.json');
                $templateDefinition = file_get_contents($templateDefinitionPath);
                try {
                    $templateDefinitionJson = json_decode(json: $templateDefinition, associative: true, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    $this->logger->critical($e->getMessage());
                    if ($verbose) {
                        throw new PuzzleTemplateRegistryBuildException($e->getMessage(), $e->getCode(), $e);
                    }
                }

                $this->definitionValidator->validateJson($templateDefinitionJson, $directory->getFilename());
            }
            $registry = new PuzzleTemplateRegistry([]);
            //TODO- Build the registry
            return $registry;
        });


    }
}
