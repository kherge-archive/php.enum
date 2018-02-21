<?php

declare(strict_types=1);

namespace Tests\KHerGe\Enum;

use KHerGe\Enum\AbstractEnum;

/**
 * An example variant.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Example extends AbstractEnum
{
    /**
     * A one variant.
     */
    const ONE = 1;

    /**
     * A two variant.
     */
    const TWO = 2;
}