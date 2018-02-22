<?php

declare(strict_types=1);

namespace Tests\KHerGe\Enum;

use DateTime;
use KHerGe\Enum\AbstractEnum;
use KHerGe\Enum\EnumException;

/**
 * An example enum.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Example extends AbstractEnum
{
    /**
     * A "ONE" element.
     */
    const ONE = 1;

    /**
     * A "TWO" element.
     */
    const TWO = 2;

    /**
     * {@inheritdoc}
     */
    protected static function validateArguments(string $name, $value, array $arguments) : void
    {
        if ($value === self::TWO) {
            if ((count($arguments) !== 1) || !($arguments[0] instanceof DateTime)) {
                throw new EnumException('The enum only accepts: [DateTime]');
            }
        }
    }
}