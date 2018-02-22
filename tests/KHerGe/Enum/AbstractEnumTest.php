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
     * Verify that an instance of the enum is returned for an element.
     */
    public function testCreateInstanceOfElement()
    {
        $element = Example::ONE();

        self::assertInstanceOf(Example::class, $element, 'The correct class was not instantiated.');
    }

    /**
     * Verify that an exception is thrown for an invalid element.
     */
    public function testInvalidElementThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The element TEST for %s is not valid.', Example::class));

        Example::TEST();
    }

    /**
     * @depends testCreateInstanceOfElement
     *
     * Verify that the enum element maps are built properly.
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
     * @depends testCreateInstanceOfElement
     *
     * Verify that the element arguments are returned.
     */
    public function testGetElementArguments()
    {
        $element = Example::ONE('a', 'b', 'c');

        self::assertEquals(['a', 'b', 'c'], $element->getArguments(), 'The arguments must be returned.');
    }

    /**
     * @depends testCreateInstanceOfElement
     *
     * Verify that the name of the element is returned.
     */
    public function testGetElementName()
    {
        $element = Example::ONE();

        self::assertEquals('ONE', $element->getName(), 'The name must be returned.');
    }

    /**
     * Verify that all element names are returned.
     */
    public function testGetAllElementNames()
    {
        self::assertEquals(['ONE', 'TWO'], Example::getNames(), 'The names must be returned.');
    }

    /**
     * @depends testCreateInstanceOfElement
     *
     * Verify that the value of the element is returned.
     */
    public function testGetElementValue()
    {
        $element = Example::ONE();

        self::assertEquals(1, $element->getValue(), 'The value must be returned.');
    }

    /**
     * Verify that all element values are returned.
     */
    public function testGetAllElementValues()
    {
        self::assertEquals([1, 2], Example::getValues(), 'The values must be returned.');
    }

    /**
     * @depends testCreateInstanceOfElement
     *
     * Verify that two elements are compared, ignoring element arguments.
     */
    public function testCompareElementsWithoutArguments()
    {
        $left = Example::ONE();
        $right = Example::ONE('a', 'b', 'c');

        self::assertTrue($left->is($right), 'The two elements must be equal.');

        $right = Example::TWO();

        self::assertFalse($left->is($right), 'The two elements must not be equal.');
    }

    /**
     * @depends testCreateInstanceOfElement
     *
     * Verify that two elements are compared, including element arguments.
     */
    public function testCompareElementsWithArguments()
    {
        $left = Example::ONE('a', 'b', 'c');
        $right = Example::ONE('a', 'b', 'c');

        self::assertTrue($left->isExactly($right), 'The two elements must be equal.');

        $right = Example::ONE();

        self::assertFalse($left->isExactly($right), 'The two elements must not be equal.');
    }

    /**
     * Verify that the name of an element is returned for its value.
     */
    public function testGetNameForValue()
    {
        self::assertEquals('ONE', Example::nameOf(1), 'The name must be returned.');
    }

    /**
     * Verify that an exception is thrown if no element uses a value.
     */
    public function testGetNameForInvalidValueThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The value 123 is not used by any element of %s.', Example::class));

        Example::nameOf(123);
    }

    /**
     * @depends testCompareElementsWithArguments
     *
     * Verify that a new instance is created for a name.
     */
    public function testCreateInstanceForName()
    {
        self::assertTrue(
            Example::ONE('a')->isExactly(Example::of('ONE', 'a')),
            'The instance must be returned for the element.'
        );
    }

    /**
     * @depends testCompareElementsWithArguments
     *
     * Verify that a new instance is created for a value.
     */
    public function testCreateInstanceForValue()
    {
        self::assertTrue(
            Example::ONE('a')->isExactly(Example::ofValue(1, 'a')),
            'The instance must be returned for the element.'
        );
    }

    /**
     * Verify that all enum elements are returned.
     */
    public function testGetAllElements()
    {
        self::assertEquals(['ONE' => 1, 'TWO' => 2], Example::toArray(), 'All elements must be returned.');
    }

    /**
     * Verify that the value of an element is returned for its name.
     */
    public function testGetValueForName()
    {
        self::assertEquals(1, Example::valueOf('ONE'), 'The value must be returned.');
    }

    /**
     * Verify that an exception is thrown if no element uses a name.
     */
    public function testGetValueForInvalidNameThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The element TEST for %s is not valid.', Example::class));

        Example::valueOf('TEST');
    }

    /**
     * Verifies the validity of an element name is checked.
     */
    public function testHasElement()
    {
        self::assertTrue(Example::has('ONE'), 'The element name must be valid.');
        self::assertFalse(Example::has('TEST'), 'The element name must not be valid.');
    }

    /**
     * Verifies the validity of an element value is checked.
     */
    public function testHasElementValue()
    {
        self::assertTrue(Example::hasValue(1), 'The element value must be valid.');
        self::assertFalse(Example::hasValue(123), 'The element value must not be valid.');
    }

    /**
     * Verify that an exception is thrown if element arguments are not valid.
     */
    public function testInvalidElementArgumentsThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage('The enum only accepts: [DateTime]');

        Example::TWO(123);
    }

    /**
     * Verify that value reuse between elements throws an exception.
     */
    public function testElementValueReuseThrowsException()
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage(sprintf('The value for ONE in %s is reused by TWO.', InvalidExample::class));

        InvalidExample::ONE();
    }
}