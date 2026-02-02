<?php

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UndecodableDynamicFieldNameException;
use App\Doctrine\Types\Exceptions\UndecodableDynamicFieldValueException;
use App\Doctrine\Types\Exceptions\UnencodableDynamicFieldValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DieRollField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DynamicPuzzleFieldInterface;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PuzzleConfigFieldType extends Type
{

    public const string NAME = 'puzzle_config_fields';
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return DynamicPuzzleFieldInterface[]
     * @throws UndecodableDynamicFieldNameException
     * @throws UndecodableDynamicFieldValueException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array {


        $decoded = json_decode($value, true);


        $values = [];

        foreach ($decoded as $delta => $configValue) {



            if (!is_array($configValue)) {
                throw new UndecodableDynamicFieldValueException(sprintf("Could not decode values for delta %d", $delta));
            }

            $this->validateConfigValueArray($configValue);


            $values[] = match ($configValue['type']) {
                ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY =>
                    new StringField(
                        $configValue['name'],
                        $configValue['label'],
                        $configValue['value'],
                    ),
                ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY => new IntegerField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                ),
                ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY => new StringArrayField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                ),
                ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY => new IntegerArrayField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                ),
                ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY => new DieRollField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                ),
                ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY => new BooleanField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                ),
                ValidPuzzleConfigOptionTypes::BOOLEAN_ARRAY_TYPE_KEY => new BooleanArrayField(
                    $configValue['name'],
                    $configValue['label'],
                    $configValue['value'],
                )

            };
        }

        return $values;
    }

    /**
     * @throws UnencodableDynamicFieldValueException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
       if (!is_array($value)) {
           throw new UnencodableDynamicFieldValueException("Could not encode values for Dynamic Configuration Field");
       }
       $values = [];
       foreach ($value as $delta => $configValue) {
           $values[$delta] = $configValue->jsonSerialize();
       }

       return json_encode($values);
    }

    /**
     * @throws UndecodableDynamicFieldValueException
     */
    private function validateConfigValueArray(array $configValueArray): void {
        if (!isset($configValueArray['name'])) {
            throw new UndecodableDynamicFieldValueException("Missing name property on config field value");
        } elseif (!is_string($configValueArray['name'])) {
            throw new UndecodableDynamicFieldValueException("Name must be a string");
        }

        if (!isset($configValueArray['type'])) {
            throw new UndecodableDynamicFieldValueException("Missing type property on config field value");
        } elseif (!is_string($configValueArray['type'])) {
            throw new UndecodableDynamicFieldValueException("Type must be a string");
        }

        if (!isset($configValueArray['value'])) {
            throw new UndecodableDynamicFieldValueException("Missing value property on config field value");
        }
    }
}
