<?php

declare(strict_types=1);

namespace KHerGe\Enum;

use ReflectionClass;

/**
 * An abstract implementation of an enum.
 *
 * A new enum is created by defining a class that extends this one. Each constant that is defined in the new class
 * becomes a variant of the enum. A variant is used by invoking the name as a static method of the enum class. This
 * produces a new instance of the enum for the specified variant.
 *
 * ```
 * class Example extends AbstractEnum
 * {
 *     const ONE = 1;
 *     const TWO = 2;
 * }
 *
 * // Create an enum instance for a specific variant.
 * $variant = Example::ONE();
 * ```
 *
 * It is recommended that enum classes be declared as `final` to prevent inheritance based issues.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractEnum
{
    /**
     * The arguments for the variant.
     *
     * @var array|null
     */
    private $arguments;

    /**
     * The enum variant maps.
     *
     * @var array
     */
    private static $maps = [
        'name' => [],
        'value' => [],
    ];

    /**
     * The name of the variant.
     *
     * @var string
     */
    private $name;

    /**
     * The value of the variant.
     *
     * @var mixed
     */
    private $value;

    /**
     * Creates a new instance for an enum variant.
     *
     * @param string $name      The name of the variant.
     * @param array  $arguments The arguments for the variant.
     *
     * @return AbstractEnum The variant.
     *
     * @throws EnumException If the variant is not valid.
     */
    public static function __callStatic(string $name, array $arguments) : AbstractEnum
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($name, self::$maps['name'][$class])) {
            throw new EnumException('The variant %s for %s is not valid.', $name, $class);
        }

        if (empty($arguments)) {
            $arguments = null;
        }

        return new $class($name, self::$maps['name'][$class][$name], $arguments);
    }

    /**
     * Returns the arguments for the variant.
     *
     * ```
     * $variant = Example::ONE('a', 'b', 'c');
     *
     * // Array ( [0] => a, [1] => b, [2] => c, )
     * print_r($variant->getArguments());
     * ```
     *
     * @return array|null The arguments, if any.
     */
    public function getArguments() : ?array
    {
        return $this->arguments;
    }

    /**
     * Returns the name of the variant.
     *
     * ```
     * $variant = Example::ONE();
     *
     * // "ONE"
     * echo $variant->getName();
     * ```
     *
     * @return string The name.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns the value of the variant.
     *
     * ```
     * $variant = Example::ONE();
     *
     * // 1
     * echo $variant->getValue();
     * ```
     *
     * @return mixed The value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Checks if another instance is equal to this one, ignoring variant arguments.
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
     * Checks if another instance is equal to this one, including variant arguments.
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
     * Returns the name of an enum variant for its value.
     *
     * ```
     * // "ONE"
     * echo Example::nameOf(1);
     * ```
     *
     * @param mixed $value The value of the variant.
     *
     * @return string The name.
     *
     * @throws EnumException If no enum variant has the value.
     */
    public static function nameOf($value) : string
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($value, self::$maps['value'][$class])) {
            throw new EnumException('The value %s is not used by any variant of %s.', $value, $class);
        }

        return self::$maps['value'][$class][$value];
    }

    /**
     * Returns the value of an enum variant for its name.
     *
     * ```
     * // 1
     * echo Example::valueOf('ONE');
     * ```
     *
     * @param string $name The name of the variant.
     *
     * @return mixed The value.
     *
     * @throws EnumException If the name is not a valid variant.
     */
    public static function valueOf(string $name)
    {
        $class = get_called_class();

        self::prepareMap($class);

        if (!array_key_exists($name, self::$maps['name'][$class])) {
            throw new EnumException('The variant %s for %s is not valid.', $name, $class);
        }

        return self::$maps['name'][$class][$name];
    }

    /**
     * Initializes the enum variant.
     *
     * @param string     $name      The name of the variant.
     * @param mixed      $value     The value of the variant.
     * @param array|null $arguments The arguments for the variant.
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
     * @throws EnumException If the value for an enum variant is reused.
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