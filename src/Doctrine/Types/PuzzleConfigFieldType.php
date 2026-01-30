<?php

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UndecodableDynamicFieldNameException;
use App\Doctrine\Types\Exceptions\UndecodableDynamicFieldValueException;
use App\Doctrine\Types\Exceptions\UnencodableDynamicFieldValueException;
use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;
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
     * @return DynamicPuzzleFieldDto[]
     * @throws UndecodableDynamicFieldNameException
     * @throws UndecodableDynamicFieldValueException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array {
        $decoded = json_decode($value, true);
        $values = [];
        foreach ($decoded as $configValue) {

            if (!is_array($configValue)) {
                throw new UndecodableDynamicFieldValueException(sprintf("Could not decode values for delta %d", $delta));
            }
            $this->validateConfigValueArray($configValue);

            $values[] = new DynamicPuzzleFieldDto(
                name: $configValue['name'],
                type: $configValue['type'],
                value: $configValue['value'],
            );
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
        $encodableArray = array_map(function ($configValue) {
            return [
                'name' => $configValue->getName(),
                'type' => $configValue->getType(),
                'value' => $configValue->getValue(),
            ];
        }, $value);
       return json_encode($encodableArray);
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
        } elseif (!is_string($configValueArray['value']) && !is_array($configValueArray['value'])) {
            throw new UndecodableDynamicFieldValueException("value must be a string or array of strings");
        }
    }
}
