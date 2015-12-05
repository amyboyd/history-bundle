<?php

namespace AmyBoyd\HistoryBundle\Enum;

use AmyBoyd\UtilityComponent\Enum\AbstractEnum;

abstract class HistoryEventType
{
    const RECORD_CREATED = 'AmyBoyd\HistoryBundle\Enum\HistoryEventType::RECORD_CREATED';

    const RECORD_UPDATED = 'AmyBoyd\HistoryBundle\Enum\HistoryEventType::RECORD_UPDATED';

    public static function validateValue($value)
    {
        if ($value !== self::RECORD_CREATED && $value !== self::RECORD_UPDATED) {
            throw new \InvalidArgumentException();
        }
    }
}
