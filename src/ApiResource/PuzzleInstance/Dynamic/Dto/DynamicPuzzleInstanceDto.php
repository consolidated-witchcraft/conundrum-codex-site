<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\BooleanArrayFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\BooleanFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\DieRollFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\UnmappableDynamicFieldTypeException;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\IntegerArrayFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\IntegerFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\StringArrayFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\StringFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\ApiResource\PuzzleTemplate\Dto\PuzzleTemplateDto;
use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\PuzzleInstance\Service\Exceptions\AttemptingToGenerateDtoForStaticPuzzleException;
use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplateConfigOptionDefinitionInterface;
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
class DynamicPuzzleInstanceDto implements \ArrayAccess
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



    /**
     * @throws AttemptingToGenerateDtoForStaticPuzzleException
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

    public function setInstanceCode(string $instanceCode): void {
        $this->instanceCode = $instanceCode;
    }

    /**
     * @param PuzzleTemplateConfigOptionDefinitionInterface[] $templateConfigurationOptions
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
    ): StringFieldDto {
        return new StringFieldDto(
            name: $textConfigurationOption->getConfigName(),
            label: $textConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromDieRollConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $dieRollConfigurationOption,
        mixed $value = null
    ): DieRollFieldDto {
        return new DieRollFieldDto(
            name: $dieRollConfigurationOption->getConfigName(),
            label: $dieRollConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromStringArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $stringArrayConfigurationOption,
        mixed $value = null
    ): StringArrayFieldDto {
        return new StringArrayFieldDto(
            name: $stringArrayConfigurationOption->getConfigName(),
            label: $stringArrayConfigurationOption->getLabel(),
            value: null
        );
    }

    public static function mapFromIntegerConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $integerConfigurationOption,
        mixed $value = null
    ): IntegerFieldDto {
        return new IntegerFieldDto(
            name: $integerConfigurationOption->getConfigName(),
            label: $integerConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromIntegerArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $integerArrayConfigurationOption,
        mixed $value = null
    ): IntegerArrayFieldDto {
        return new IntegerArrayFieldDto(
            name: $integerArrayConfigurationOption->getConfigName(),
            label: $integerArrayConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromBooleanConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $booleanConfigurationOption,
        mixed $value = null
    ): BooleanFieldDto {
        return new BooleanFieldDto(
            name: $booleanConfigurationOption->getConfigName(),
            label: $booleanConfigurationOption->getLabel(),
            value: $value
        );
    }

    public static function mapFromBooleanArrayConfig(
        PuzzleTemplateConfigOptionDefinitionInterface $booleanArrayConfigurationOption,
        mixed $value = null
    ): BooleanArrayFieldDto {
        return new BooleanArrayFieldDto(
            name: $booleanArrayConfigurationOption->getConfigName(),
            label: $booleanArrayConfigurationOption->getLabel(),
            value: $value
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->config[$offset]->getValue();
        }
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset): void
    {
        // TODO: Implement offsetUnset() method.
    }
}
