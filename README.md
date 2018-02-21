[![Build Status](https://travis-ci.org/kherge/php.enum.svg?branch=master)](https://travis-ci.org/kherge/php.enum)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=php.enum&metric=alert_status)](https://sonarcloud.io/dashboard?id=php.enum)

Enum
=========

An easy to use and feature packed enum implementation for PHP.

Usage
-----

```php
use KHerGe\Enum\AbstractEnum;

/**
 * An example enum.
 */
class Example extends AbstractEnum
{
    const ONE = 1;
    const TWO = 'two';
}

// Get the value of an enum variant for its name.
$value = Example::valueOf('ONE');

// Get the name of an enum variant for its value.
$name = Example::nameOf('two');

// Create an instance of the enum for a variant.
$one = Example::ONE();

/**
 * Prints the name and value of a variant.
 */
function display(Example $enum)
{
    echo $enum->getName(), ' = ', $enum->getValue(), "\n";
}

// Name and value can be retrieved from an instance.
display($one);

// Create an instance of the enum for a variant and include arguments.
$two = Example::TWO('a', 'b', 'c');

/**
 * Prints the arguments for a variant.
 */
function arguments(Example $enum)
{
    foreach ($enum->getArguments() as $argument) {
        echo ' - ', $argument, "\n";
    }
}

// Access the arguments for the variant.
arguments($two);

// Instances can also be compared for equality (ignores arguments).
if (Example::ONE()->is(Example::ONE())) {
    // ... equivalent ...
}

// Instances can also be compared for equality (with arguments).
if (!Example::ONE()->isExactly(Example::ONE('a', 'b', 'c'))) {
    // ... not equivalent ...
}
```

Requirements
------------

- PHP 7.1 or greater

Installation
------------

Use Composer to install the package as a dependency.

    $ composer require kherge/enum

Testing
-------

Use PHPUnit 7.0 to run the test suite.

    $ phpunit

License
-------

This library is available under the Apache 2.0 and MIT licenses.