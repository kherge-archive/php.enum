<?php

declare(strict_types=1);

namespace Tests\KHerGe\Enum;

use KHerGe\Enum\AbstractEnum;
use KHerGe\Enum\EnumException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Verifies that the abstract enum implementation functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class AbstractEnumTest extends TestCase
{
    /**
     * Verify that an instance of the enum is returned for a variant.
     */
    public function testCreateInstanceOfVariant()
    {
        $variant = Example::ONE();

        self::assertInstanceOf(Example::class, $variant, 'The correct class was not instantiated.');
    }

    /**
     * Verify that an exception is thrown for an invalid variant.
     */
    public function testInvalidVariantThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The variant TEST for %s is not valid.', Example::class));

        Example::TEST();
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that the enum variant maps are built properly.
     */
    public function testMapsBuiltProperly()
    {
        self::assertEquals(
            [
                'maps' => [
                    'name' => [
                        Example::class => [
                            'ONE' => 1,
                            'TWO' => 2,
                        ]
                    ],
                    'value' => [
                        Example::class => [
                            1 => 'ONE',
                            2 => 'TWO',
                        ]
                    ]
                ]
            ],
            (new ReflectionClass(AbstractEnum::class))->getStaticProperties(),
            'The map must be built properly.'
        );
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that the variant arguments are returned.
     */
    public function testGetVariantArguments()
    {
        $variant = Example::ONE('a', 'b', 'c');

        self::assertEquals(['a', 'b', 'c'], $variant->getArguments(), 'The arguments must be returned.');
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that the name of the variant is returned.
     */
    public function testGetVariantName()
    {
        $variant = Example::ONE();

        self::assertEquals('ONE', $variant->getName(), 'The name must be returned.');
    }

    /**
     * Verify that all variant names are returned.
     */
    public function testGetAllVariantNames()
    {
        self::assertEquals(['ONE', 'TWO'], Example::getNames(), 'The names must be returned.');
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that the value of the variant is returned.
     */
    public function testGetVariantValue()
    {
        $variant = Example::ONE();

        self::assertEquals(1, $variant->getValue(), 'The value must be returned.');
    }

    /**
     * Verify that all variant values are returned.
     */
    public function testGetAllVariantValues()
    {
        self::assertEquals([1, 2], Example::getValues(), 'The values must be returned.');
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that two variants are compared, ignoring variant arguments.
     */
    public function testCompareVariantsWithoutArguments()
    {
        $left = Example::ONE();
        $right = Example::ONE('a', 'b', 'c');

        self::assertTrue($left->is($right), 'The two variants must be equal.');

        $right = Example::TWO();

        self::assertFalse($left->is($right), 'The two variants must not be equal.');
    }

    /**
     * @depends testCreateInstanceOfVariant
     *
     * Verify that two variants are compared, including variant arguments.
     */
    public function testCompareVariantsWithArguments()
    {
        $left = Example::ONE('a', 'b', 'c');
        $right = Example::ONE('a', 'b', 'c');

        self::assertTrue($left->isExactly($right), 'The two variants must be equal.');

        $right = Example::ONE();

        self::assertFalse($left->isExactly($right), 'The two variants must not be equal.');
    }

    /**
     * Verify that the name of a variant is returned for its value.
     */
    public function testGetNameForValue()
    {
        self::assertEquals('ONE', Example::nameOf(1), 'The name must be returned.');
    }

    /**
     * Verify that an exception is thrown if no variant uses a value.
     */
    public function testGetNameForInvalidValueThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The value 123 is not used by any variant of %s.', Example::class));

        Example::nameOf(123);
    }

    /**
     * @depends testCompareVariantsWithArguments
     *
     * Verify that a new instance is created for a name.
     */
    public function testCreateInstanceForName()
    {
        self::assertTrue(
            Example::ONE('a')->isExactly(Example::of('ONE', 'a')),
            'The instance must be returned for the variant.'
        );
    }

    /**
     * @depends testCompareVariantsWithArguments
     *
     * Verify that a new instance is created for a value.
     */
    public function testCreateInstanceForValue()
    {
        self::assertTrue(
            Example::ONE('a')->isExactly(Example::ofValue(1, 'a')),
            'The instance must be returned for the variant.'
        );
    }

    /**
     * Verify that all enum variants are returned.
     */
    public function testGetAllVariants()
    {
        self::assertEquals(['ONE' => 1, 'TWO' => 2], Example::toArray(), 'All variants must be returned.');
    }

    /**
     * Verify that the value of a variant is returned for its name.
     */
    public function testGetValueForName()
    {
        self::assertEquals(1, Example::valueOf('ONE'), 'The value must be returned.');
    }

    /**
     * Verify that an exception is thrown if no variant uses a name.
     */
    public function testGetValueForInvalidNameThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The variant TEST for %s is not valid.', Example::class));

        Example::valueOf('TEST');
    }

    /**
     * Verifies the validity of a variant name is checked.
     */
    public function testHasVariant()
    {
        self::assertTrue(Example::has('ONE'), 'The variant name must be valid.');
        self::assertFalse(Example::has('TEST'), 'The variant name must not be valid.');
    }

    /**
     * Verifies the validity of a variant value is checked.
     */
    public function testHasVariantValue()
    {
        self::assertTrue(Example::hasValue(1), 'The variant value must be valid.');
        self::assertFalse(Example::hasValue(123), 'The variant value must not be valid.');
    }

    /**
     * Verify that value reuse between variants throws an exception.
     */
    public function testVariantValueReuseThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The value for ONE in %s is reused by TWO.', InvalidExample::class));

        InvalidExample::ONE();
    }
}