<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleTemplate\Dto\PuzzleTemplateDto;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DieRollField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\UnmappableDynamicFieldTypeException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleInstance\Domain\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleInstance\Service\Exceptions\AttemptingToGenerateDtoForStaticPuzzleException;
use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplateConfigOptionDefinitionInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;

#[ApiResource(
    uriTemplate: '/puzzles/instances/dynamic/{templateSlug}/{instanceCode}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'templateSlug' => new Link(
            fromProperty: 'templateSlug',
            fromClass: PuzzleTemplateDto::class
        ),
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: DynamicPuzzleInstanceDto::class
        )
    ],
    stateless: false,
    provider: DynamicPuzzleInstanceProvider::class
)]
class DynamicPuzzleInstanceDto
{
    private function __construct(
        public readonly Game $game,
        public string $puzzleName,
        public string $puzzleDescription,
        public array $config,
        public ? string $instanceCode = null,
        public ?\DateTimeInterface $publicationDate = null
    ) {
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    /**
     * @throws AttemptingToGenerateDtoForStaticPuzzleException|UnmappableDynamicFieldTypeException
     */
    public static function makeFromPuzzleTemplate(
        string $puzzleName,
        string $puzzleDescription,
        Game $game,
        PuzzleTemplate $puzzleTemplate
    ): DynamicPuzzleInstanceDto {
        if ($puzzleTemplate->isStatic()) {
            throw new AttemptingToGenerateDtoForStaticPuzzleException($puzzleTemplate->getTitle());
        }
        return new static(
            game: $game,
            puzzleName: $puzzleName,
            puzzleDescription: $puzzleDescription,
            config: self::mapConfig($puzzleTemplate),
        );
    }

    /**
     * @throws UnmappableDynamicFieldTypeException
     * @throws AttemptingToGenerateDtoForStaticPuzzleException
     * @throws PuzzleTemplateNotMappedException
     */
    public static function makeFromPuzzleInstance(PuzzleInstance $puzzleInstance): DynamicPuzzleInstanceDto {
        if ($puzzleInstance->getTemplate()->isStatic()) {
            throw new AttemptingToGenerateDtoForStaticPuzzleException($puzzleInstance->getTemplate()->getTitle());
        }
        return new static(
            game: $puzzleInstance->getGame(),
            puzzleName: $puzzleInstance->getName(),
            puzzleDescription: $puzzleInstance->getDescription(),
            config: self::mapConfig($puzzleInstance->getTemplate()),
        );
    }

    public function setInstanceCode(string $instanceCode): void {
        $this->instanceCode = $instanceCode;
    }

    /**
     * @throws UnmappableDynamicFieldTypeException
     */
    public function setConfigFieldValue(string $configFieldName, mixed $value): void {
        if (!array_key_exists($configFieldName, $this->config)) {
            throw new UnmappableDynamicFieldTypeException($configFieldName);
        }
        $this->config[$configFieldName]->setValue($value);
    }

    /**
     * @param PuzzleTemplate $template
     * @return array
     * @throws UnmappableDynamicFieldTypeException
     */
    private static function mapConfig(PuzzleTemplate $template): array {
        $mappedConfig = [];
        foreach ($template->getConfiguration() as $templateConfigurationOption) {
            $mappedConfig[$templateConfigurationOption->getConfigName()] = match ($templateConfigurationOption->getType()) {
                'string' => self::mapFromStringConfig($templateConfigurationOption),
                'dieRoll' => self::mapFromDieRollConfig($templateConfigurationOption),
                'stringArray' => self::mapFromStringArrayConfig($templateConfigurationOption),
                'integer' => self::mapFromIntegerConfig($templateConfigurationOption),
                'integerArray' => self::mapFromIntegerArrayConfig($templateConfigurationOption),
                'boolean' => self::mapFromBooleanConfig($templateConfigurationOption),
                'booleanArray' => self::mapFromBooleanArrayConfig($templateConfigurationOption),
                default => throw new UnmappableDynamicFieldTypeException($templateConfigurationOption->getType())
            };

        }
        return $mappedConfig;
    }

    public static function mapFromStringConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $textConfigurationOption,
        mixed $value = null
    ): StringField {
        return new StringField(
            name: $textConfigurationOption->getConfigName(),
            label: $textConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromDieRollConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $dieRollConfigurationOption,
        mixed $value = null
    ): DieRollField {
        return new DieRollField(
            name: $dieRollConfigurationOption->getConfigName(),
            label: $dieRollConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromStringArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $stringArrayConfigurationOption,
        mixed $value = null
    ): StringArrayField {
        return new StringArrayField(
            name: $stringArrayConfigurationOption->getConfigName(),
            label: $stringArrayConfigurationOption->getLabel(),
            value: null
        );
    }

    public static function mapFromIntegerConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $integerConfigurationOption,
        mixed $value = null
    ): IntegerField {
        return new IntegerField(
            name: $integerConfigurationOption->getConfigName(),
            label: $integerConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromIntegerArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $integerArrayConfigurationOption,
        mixed $value = null
    ): IntegerArrayField {
        return new IntegerArrayField(
            name: $integerArrayConfigurationOption->getConfigName(),
            label: $integerArrayConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromBooleanConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $booleanConfigurationOption,
        mixed $value = null
    ): BooleanField {
        return new BooleanField(
            name: $booleanConfigurationOption->getConfigName(),
            label: $booleanConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromBooleanArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $booleanArrayConfigurationOption,
        mixed $value = null
    ): BooleanArrayField {
        return new BooleanArrayField(
            name: $booleanArrayConfigurationOption->getConfigName(),
            label: $booleanArrayConfigurationOption->getLabel(),
            value: $value
        );
    }
}
