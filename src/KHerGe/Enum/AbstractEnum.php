<?php

declare(strict_types=1);

namespace KHerGe\Enum;

use ReflectionClass;

/**
 * An abstract implementation of an enum.
 *
 * A new enum is created by defining a class that extends this one. Each constant that is defined in the new class
 * becomes an element of the enum. An element is used by invoking the name as a static method of the enum class. This
 * produces a new instance of the enum for the specified element.
 *
 * ```
 * class Example extends AbstractEnum
 * {
 *     const ONE = 1;
 *     const TWO = 2;
 * }
 *
 * // Create an enum instance for a specific element.
 * $element = Example::ONE();
 * ```
 *
 * It is recommended that enum classes be declared as `final` to prevent inheritance based issues.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractEnum
{
    /**
     * The arguments for the element.
     *
     * @var array|null
     */
    private $arguments;

    /**
     * The enum element maps.
     *
     * @var array
     */
    private static $maps = [
        'name' => [],
        'value' => [],
    ];

    /**
     * The name of the element.
     *
     * @var string
     */
    private $name;

    /**
     * The value of the element.
     *
     * @var mixed
     */
    private $value;

    /**
     * Creates a new instance for an enum element.
     *
     * @param string $name      The name of the element.
     * @param array  $arguments The arguments for the element.
     *
     * @return static The element.
     *
     * @throws EnumException If the element is not valid.
     */
    public static function __callStatic(string $name, array $arguments) : AbstractEnum
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($name, self::$maps['name'][$class])) {
            throw new EnumException('The element %s for %s is not valid.', $name, $class);
        }

        if (empty($arguments)) {
            $arguments = null;
        } else {
            static::validateArguments($name, self::$maps['name'][$class][$name], $arguments);
        }

        return new $class($name, self::$maps['name'][$class][$name], $arguments);
    }

    /**
     * Returns the arguments for the element.
     *
     * ```
     * $element = Example::ONE('a', 'b', 'c');
     *
     * // Array ( [0] => a, [1] => b, [2] => c, )
     * print_r($element->getArguments());
     * ```
     *
     * @return array|null The arguments, if any.
     */
    public function getArguments() : ?array
    {
        return $this->arguments;
    }

    /**
     * Returns the name of the element.
     *
     * ```
     * $element = Example::ONE();
     *
     * // "ONE"
     * echo $element->getName();
     * ```
     *
     * @return string The name.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns all of the enum element names.
     *
     * ```
     * // Array ( [0] => ONE, [1] => TWO )
     * print_r(Example::getNames());
     * ```
     *
     * @return string[] The names.
     */
    public static function getNames() : array
    {
        $class = get_called_class();

        self::prepareMap($class);

        return array_keys(self::$maps['name'][$class]);
    }

    /**
     * Returns the value of the element.
     *
     * ```
     * $element = Example::ONE();
     *
     * // 1
     * echo $element->getValue();
     * ```
     *
     * @return mixed The value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns all of the enum element values.
     *
     * ```
     * // Array ( [0] => 1, [1] => 2 )
     * print_r(Example::getValues());
     * ```
     *
     * @return mixed[] The values.
     */
    public static function getValues() : array
    {
        $class = get_called_class();

        self::prepareMap($class);

        return array_values(self::$maps['name'][$class]);
    }

    /**
     * Checks if the name of a element is valid.
     *
     * @param string $name The name.
     *
     * @return boolean Returns `true` if valid or `false` if not.
     */
    public static function has(string $name) : bool
    {
        $class = get_called_class();

        self::prepareMap($class);

        return array_key_exists($name, self::$maps['name'][$class]);
    }

    /**
     * Checks if the value of a element is valid.
     *
     * @param mixed $value The value.
     *
     * @return boolean Returns `true` if valid or `false` if not.
     */
    public static function hasValue($value) : bool
    {
        $class = get_called_class();

        self::prepareMap($class);

        return array_key_exists($value, self::$maps['value'][$class]);
    }

    /**
     * Checks if another instance is equal to this one, ignoring element arguments.
     *
     * ```
     * $left = Example::ONE();
     * $right = Example::ONE('a', 'b', 'c');
     *
     * if ($left->is($right)) {
     *     // ... equivalent ...
     * }
     * ```
     *
     * @param AbstractEnum $instance The enum instance.
     *
     * @return boolean Returns `true` if equal or `false` if not.
     */
    public function is(AbstractEnum $instance) : bool
    {
        return (get_class($this) === get_class($instance)) && ($this->name === $instance->getName());
    }

    /**
     * Checks if another instance is equal to this one, including element arguments.
     *
     * ```
     * $left = Example::ONE();
     * $right = Example::ONE('a', 'b', 'c');
     *
     * if (!$left->isExactly($right)) {
     *     // ... not equivalent ...
     * }
     * ```
     *
     * @param AbstractEnum $instance The enum instance.
     *
     * @return boolean Returns `true` if equal or `false` if not.
     */
    public function isExactly(AbstractEnum $instance) : bool
    {
        return (get_class($this) === get_class($instance))
            && ($this->name === $instance->getName())
            && ($this->arguments === $instance->getArguments());
    }

    /**
     * Returns the name of an enum element for its value.
     *
     * ```
     * // "ONE"
     * echo Example::nameOf(1);
     * ```
     *
     * @param mixed $value The value of the element.
     *
     * @return string The name.
     *
     * @throws EnumException If no enum element has the value.
     */
    public static function nameOf($value) : string
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($value, self::$maps['value'][$class])) {
            throw new EnumException('The value %s is not used by any element of %s.', $value, $class);
        }

        return self::$maps['value'][$class][$value];
    }

    /**
     * Creates a new enum element instance for a name.
     *
     * @param string $name         The name.
     * @param mixed  $argument,... An argument.
     *
     * @return static The new instance.
     */
    public static function of(string $name, ...$arguments) : AbstractEnum
    {
        $class = get_called_class();

        return $class::__callStatic($name, $arguments);
    }

    /**
     * Creates a new enum element instance for a value.
     *
     * @param mixed $value        The value.
     * @param mixed $argument,... An argument.
     *
     * @return static The new instance.
     */
    public static function ofValue($value, ...$arguments) : AbstractEnum
    {
        $class = get_called_class();

        return $class::__callStatic($class::nameOf($value), $arguments);
    }

    /**
     * Returns all of the elements for the enum as NAME => VALUE map.
     *
     * ```
     * // Array ( [ONE] => 1, [TWO] => 2 )
     * print_r(Example::toArray());
     * ```
     *
     * @return mixed[] The elements.
     */
    public static function toArray() : array
    {
        $class = get_called_class();

        self::prepareMap($class);

        return self::$maps['name'][$class];
    }

    /**
     * Returns the value of an enum elements for its name.
     *
     * ```
     * // 1
     * echo Example::valueOf('ONE');
     * ```
     *
     * @param string $name The name of the elements.
     *
     * @return mixed The value.
     *
     * @throws EnumException If the name is not a valid elements.
     */
    public static function valueOf(string $name)
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($name, self::$maps['name'][$class])) {
            throw new EnumException('The element %s for %s is not valid.', $name, $class);
        }

        return self::$maps['name'][$class][$name];
    }

    /**
     * Validates the arguments to be used for a new instance.
     *
     * @param string  $name      The name of the element.
     * @param mixed   $value     The value of the element.
     * @param mixed[] $arguments The arguments for the element.
     *
     * @throws EnumException If the arguments are not valid.
     */
    protected static function validateArguments(string $name, $value, array $arguments) : void
    {
        // Implementation left to child class.
    }

    /**
     * Initializes the enum element.
     *
     * @param string     $name      The name of the element.
     * @param mixed      $value     The value of the element.
     * @param array|null $arguments The arguments for the element.
     */
    private function __construct(string $name, $value, ?array $arguments)
    {
        $this->arguments = $arguments;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Initializes the map for the enum class.
     *
     * @param string $class The name of the enum class.
     *
     * @throws EnumException If the value for an enum element is reused.
     */
    private static function prepareMap(string $class) : void
    {
        if (isset(self::$maps['name'][$class])) {
            return;
        }

        self::$maps['name'][$class] = [];
        self::$maps['value'][$class] = [];

        foreach ((new ReflectionClass($class))->getConstants() as $name => $value) {
            if (array_key_exists($value, self::$maps['value'][$class])) {
                throw new EnumException(
                    'The value for %s in %s is reused by %s.',
                    self::$maps['value'][$class][$value],
                    $class,
                    $name
                );
            }

            self::$maps['name'][$class][$name] = $value;
            self::$maps['value'][$class][$value] = $name;
        }
    }
}
