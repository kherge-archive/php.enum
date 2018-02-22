<?php

declare(strict_types=1);

namespace Tests\KHerGe\Enum;

use KHerGe\Enum\AbstractEnum;

/**
 * An invalid example.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class InvalidExample extends AbstractEnum
{
    /**
     * A "ONE" element.
     */
    const ONE = 1;

    /**
     * A "TWO" element.
     */
    const TWO = 1;
}