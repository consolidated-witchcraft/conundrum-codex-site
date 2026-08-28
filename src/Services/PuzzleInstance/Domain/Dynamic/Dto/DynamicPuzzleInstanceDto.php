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
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\ClueArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DieRollField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\UnmappableDynamicFieldTypeException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\KeyValueListField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DynamicPuzzleFieldInterface;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleInstance\Domain\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleInstance\Service\Exceptions\AttemptingToGenerateDtoForStaticPuzzleException;
use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplateConfigOptionDefinitionInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;


class DynamicPuzzleInstanceDto
{
    private function __construct(
        public readonly Game $game,
        public string $puzzleName,
        public string $puzzleDescription,
        /** @var array<string, DynamicPuzzleFieldInterface> */
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
    ): self {
        if ($puzzleTemplate->isStatic()) {
            throw new AttemptingToGenerateDtoForStaticPuzzleException($puzzleTemplate->getTitle());
        }
        return new self(
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
    public static function makeFromPuzzleInstance(PuzzleInstance $puzzleInstance): self {
        if ($puzzleInstance->getTemplate()->isStatic()) {
            throw new AttemptingToGenerateDtoForStaticPuzzleException($puzzleInstance->getTemplate()->getTitle());
        }
        return new self(
            game: $puzzleInstance->getGame(),
            puzzleName: $puzzleInstance->getName(),
            puzzleDescription: $puzzleInstance->getDescription(),
            config: self::mapConfig($puzzleInstance->getTemplate(), $puzzleInstance),
            instanceCode: $puzzleInstance->getInstanceCode(),
            publicationDate: $puzzleInstance->getPublicationDate(),
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

    public function getConfigFieldValue(string $configFieldName): mixed {
        if (array_key_exists($configFieldName, $this->config)) {
            return $this->config[$configFieldName]->getValue();
        }
        return null;
    }

    /**
     * @param PuzzleTemplate $template
     * @param PuzzleInstance|null $instance
     * @return array<string, DynamicPuzzleFieldInterface>
     * @throws UnmappableDynamicFieldTypeException
     */
    private static function mapConfig(PuzzleTemplate $template, ?PuzzleInstance $instance = null): array {

        $mappedConfig = [];
        foreach ($template->getConfiguration() as $templateConfigurationOption) {
            $mappedConfig[$templateConfigurationOption->getConfigName()] = match ($templateConfigurationOption->getType()) {
                ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY => self::mapFromStringConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY => self::mapFromDieRollConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY => self::mapFromStringArrayConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY => self::mapFromIntegerConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY => self::mapFromIntegerArrayConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY => self::mapFromBooleanConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::BOOLEAN_ARRAY_TYPE_KEY => self::mapFromBooleanArrayConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
                ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY => self::mapFromKeyValueListConfig($templateConfigurationOption, $instance?->getConfigValue($templateConfigurationOption->getConfigName())),
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
            value: $value
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

    public static function mapFromKeyValueListConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $booleanArrayConfigurationOption,
        mixed $value = null
    ): KeyValueListField {
        return new KeyValueListField(
            name: $booleanArrayConfigurationOption->getConfigName(),
            label: $booleanArrayConfigurationOption->getLabel(),
            value: $value
        );
    }

}
