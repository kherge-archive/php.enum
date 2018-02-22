[![Build Status](https://travis-ci.org/kherge/php.enum.svg?branch=master)](https://travis-ci.org/kherge/php.enum)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=php.enum&metric=alert_status)](https://sonarcloud.io/dashboard?id=php.enum)

Enum
=========

An easy to use and feature packed enum implementation for PHP.

Usage
-----

```php
use KHerGe\Enum\AbstractEnum;

final class System extends AbstractEnum
{
    const LINUX = 1;
    const MACOS = 3;
    const WINDOWS = 2;
}

$system = System::LINUX();

if ($system instanceof System) {
    // Oh, yes...
}
```

Requirements
------------

- PHP 7.1 or greater

Installation
------------

Use Composer to install the package as a dependency.

    $ composer require kherge/enum

Documentation
-------------

## Creating an Enum

A new enum is created by creating a new class that extends `KHerGe\Enum\AbstractEnum`.

```php
use KHerGe\Enum\AbstractEnum;

/**
 * My example enum.
 */
final class Example extends AbstractEnum
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
     * A "THREE" element.
     */
    const THREE = 3;
}
```

## Using Elements

Elements are used by creating instances of the enum class they belong to. To create a new instance, the name of the element is statically invoked for the enum class. This allows parameter type hints to limit only to valid instances of the enum.

```php
$one = Example::ONE();

if ($one instanceof Example) {
    // it is an instance
}
```

An instance can be created if you have the name of an element,

```php
$one = Example::of('ONE');
```

or its value.

```php
$one = Example::ofValue(1);
```

### Element Attributes

You can retrieve the name of an element from its instance

```php
// "ONE"
print_r($one->getName());
```

as well as its value.

```php
// 1
print_r($one->getValue());
```

### Element Arguments

Optionally, you may include arguments with your element instances.

```php
$one = Example::ONE('something');

// Array ( [0] => something )
print_r($one->getArguments());
```

#### Validating Element Arguments

Argument validation is also supported for enums.

```php
class Example extends AbstractEnum
{
    const ONE = 1;
    const TWO = 2;

    protected static function validateArguments(string $name, $value, array $arguments) : void
    {
        // How $arguments is validated is up to yo.
    }
}
```

## Getting Elements

You can retrieve a list of all the element names,

```php
// Array ( [0] => ONE, [1] => TWO )
print_r(Example::getNames());
```

as well as their values.

```php
// Array ( [0] => 1, [1] => 2 )
print_r(Example::getValues());
```

It is also possible to retrieve a map of names to values.

```php
// Array ( [ONE] =>, [TWO] => 2 )
print_r(Example::toArray());
```

The name of the element can be retrived for its value,

```php
// "ONE"
print_r(Example::nameOf(1));
```

and the value of the element can be retrieved for its name.

```php
// 1
print_r(Example::valueOf('ONE'));
```

## Comparing Elements

It is possible to compare instances of enum elements.

```php
$element = Example::ONE();

if (Example::ONE()->is($element)) {
    // It is ONE.
}

if (Example::ONE('a', 'b', 'c')->is($element)) {
    // It is ONE, even if the arguments are different.
}
```

It is also possible to compare compare instances with their arguments.

```php
$element = Example::ONE('a', 'b', 'c');

if (Example::ONE()->isExactly($element)) {
    // Never make it this far, does not match.
}

if (Example::ONE('a', 'b', 'c')->is($element)) {
    // It is a perfect match.
}
```

If you need to loosely compare two enums with objects in their arguments, you can use `isLoosely()`.

```php
$leftDate = new DateTime();
$rightDate = clone $leftDate;

$left = Example::ONE($leftDate);
$right = Example::ONE($rightDate);

if ($left->isLoosely($right)) {
    // They are loosely equivalent.
}
```

## Checking Elements

The name of an element element can be validated,

```php
if (Example::has('ONE')) {
    // It is valid.
}
```

as well as its value.

```php
if (Example::hasValue(1)) {
    // It is valid.
}
```

Testing
-------

Use PHPUnit 7.0 to run the test suite.

    $ phpunit

License
-------

This library is available under the Apache 2.0 and MIT licenses.