<?php

namespace App\Services\PuzzleTemplate\Service;

class ValidPuzzleConfigOptionTypes
{

    public const string STRING_TYPE_KEY = 'string';

    public const string INTEGER_TYPE_KEY = 'integer';

    public const string STRING_ARRAY_TYPE_KEY = 'stringArray';

    public const string INTEGER_ARRAY_TYPE_KEY = 'integerArray';

    public const string DIE_ROLL_TYPE_KEY = 'dieRoll';

    public const string ROUTE_TYPE_KEY = 'route';

    public const string BOOLEAN_TYPE_KEY = 'boolean';

    public const string BOOLEAN_ARRAY_TYPE_KEY = 'booleanArray';

    public const string KEY_VALUE_LIST_TYPE_KEY = 'keyValueList';

    public const string CLUE_ARRAY_KEY = 'clueArray';
    /**
     * These are the valid value for the 'type' field in the above config option field.
     */
    public const array KNOWN_CONFIG_OPTION_TYPES = [
        self::STRING_TYPE_KEY,
        self::INTEGER_TYPE_KEY,
        self::STRING_ARRAY_TYPE_KEY,
        self::INTEGER_ARRAY_TYPE_KEY,
        self::DIE_ROLL_TYPE_KEY,
        self::ROUTE_TYPE_KEY,
        self::BOOLEAN_TYPE_KEY,
        self::BOOLEAN_ARRAY_TYPE_KEY,
        self::KEY_VALUE_LIST_TYPE_KEY,
        self::CLUE_ARRAY_KEY,
    ];
}
