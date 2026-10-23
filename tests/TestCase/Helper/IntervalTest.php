<?php

declare(strict_types=1);

namespace Tests\TestCase\Helper;

use Cadasto\OpenEHR\BMM\Helper\Interval;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-type State array{lower: ?int, upper: ?int, lowerUnbounded: ?bool, upperUnbounded: ?bool}
 */
final class IntervalTest extends TestCase
{
    public function testToArrayDefaultValues(): void
    {
        $interval = new Interval();

        $result = $interval->toArray();

        self::assertSame(0, $result['lower']);
        self::assertTrue($result['upper_unbounded']);
        self::assertArrayNotHasKey('upper', $result);
        self::assertArrayNotHasKey('lower_unbounded', $result);
    }

    public function testToArrayExplicitValues(): void
    {
        $interval = new Interval(lower: 1, upper: 5, lowerUnbounded: false, upperUnbounded: false);

        $result = $interval->toArray();

        self::assertSame(1, $result['lower']);
        self::assertSame(5, $result['upper']);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     */
    #[DataProvider('readingProvider')]
    public function testFromArrayReading(array $data, array $expected): void
    {
        self::assertSame($expected, Interval::fromArray($data)->toArray());
    }

    /**
     * @return array<string, array{array<string, mixed>, array<string, mixed>}>
     */
    public static function readingProvider(): array
    {
        return [
            'upper given, flag omitted' => [
                ['lower' => 2, 'upper' => 4],
                ['lower' => 2, 'upper' => 4],
            ],
            'upper given, flag false' => [
                ['lower' => 2, 'upper' => 4, 'upper_unbounded' => false],
                ['lower' => 2, 'upper' => 4],
            ],
            'upper omitted, flag omitted' => [
                ['lower' => 1],
                ['lower' => 1, 'upper_unbounded' => true],
            ],
            'upper null, flag omitted' => [
                ['lower' => 1, 'upper' => null],
                ['lower' => 1, 'upper_unbounded' => true],
            ],
            'upper omitted, flag false' => [
                ['lower' => 1, 'upper_unbounded' => false],
                ['lower' => 1, 'upper_unbounded' => true],
            ],
            'upper given, flag true' => [
                ['lower' => 2, 'upper' => 4, 'upper_unbounded' => true],
                ['lower' => 2, 'upper_unbounded' => true],
            ],
            'empty input' => [
                [],
                ['lower' => 0, 'upper_unbounded' => true],
            ],
            'lower omitted' => [
                ['upper' => 4],
                ['lower' => 0, 'upper' => 4],
            ],
            'lower unbounded' => [
                ['upper' => 4, 'lower_unbounded' => true],
                ['lower' => 0, 'upper' => 4],
            ],
            'lower given, lower unbounded' => [
                ['lower' => 3, 'upper' => 4, 'lower_unbounded' => true],
                ['lower' => 0, 'upper' => 4],
            ],
            'lower negative' => [
                ['lower' => -1, 'upper' => 4],
                ['lower' => 0, 'upper' => 4],
            ],
            'zero only' => [
                ['lower' => 0, 'upper' => 0],
                ['lower' => 0, 'upper' => 0],
            ],
            'lower excluded' => [
                ['lower' => 0, 'upper' => 5, 'lower_included' => false],
                ['lower' => 1, 'upper' => 5],
            ],
            'upper excluded' => [
                ['lower' => 0, 'upper' => 5, 'upper_included' => false],
                ['lower' => 0, 'upper' => 4],
            ],
            'both included' => [
                ['lower' => 2, 'upper' => 4, 'lower_included' => true, 'upper_included' => true],
                ['lower' => 2, 'upper' => 4],
            ],
            'lower excluded, lower omitted' => [
                ['upper' => 4, 'lower_included' => false],
                ['lower' => 0, 'upper' => 4],
            ],
            'upper excluded, upper unbounded' => [
                ['lower' => 1, 'upper_unbounded' => true, 'upper_included' => false],
                ['lower' => 1, 'upper_unbounded' => true],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('noCountProvider')]
    public function testFromArrayRejectsIntervalWithNoCount(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);

        Interval::fromArray($data);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function noCountProvider(): array
    {
        return [
            'upper excluded at lower' => [['lower' => 0, 'upper' => 0, 'upper_included' => false]],
            'both excluded at one value' => [
                ['lower' => 5, 'upper' => 5, 'lower_included' => false, 'upper_included' => false],
            ],
            'upper below lower' => [['lower' => 6, 'upper' => 4]],
            'upper negative' => [['upper' => -1]],
        ];
    }

    public function testConstructorRejectsIntervalWithNoCount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Interval(lower: 2, upper: 1);
    }

    /**
     * @param State $expected
     */
    #[DataProvider('constructorProvider')]
    public function testConstructorNormalises(Interval $interval, array $expected): void
    {
        self::assertSame($expected, [
            'lower' => $interval->lower,
            'upper' => $interval->upper,
            'lowerUnbounded' => $interval->lowerUnbounded,
            'upperUnbounded' => $interval->upperUnbounded,
        ]);
    }

    /**
     * @return array<string, array{Interval, State}>
     */
    public static function constructorProvider(): array
    {
        return [
            'upper given' => [
                new Interval(lower: 2, upper: 4),
                ['lower' => 2, 'upper' => 4, 'lowerUnbounded' => false, 'upperUnbounded' => false],
            ],
            'upper given, upper unbounded' => [
                new Interval(lower: 2, upper: 4, upperUnbounded: true),
                ['lower' => 2, 'upper' => null, 'lowerUnbounded' => false, 'upperUnbounded' => true],
            ],
            'upper omitted, upper bounded' => [
                new Interval(lower: 1, upperUnbounded: false),
                ['lower' => 1, 'upper' => null, 'lowerUnbounded' => false, 'upperUnbounded' => true],
            ],
            'lower null' => [
                new Interval(lower: null, upper: 4),
                ['lower' => 0, 'upper' => 4, 'lowerUnbounded' => false, 'upperUnbounded' => false],
            ],
            'lower given, lower unbounded' => [
                new Interval(lower: 3, upper: 4, lowerUnbounded: true),
                ['lower' => 0, 'upper' => 4, 'lowerUnbounded' => false, 'upperUnbounded' => false],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('roundTripProvider')]
    public function testFromArrayToArrayRoundTrip(array $data): void
    {
        self::assertSame($data, Interval::fromArray($data)->toArray());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function roundTripProvider(): array
    {
        return [
            'bounded |2..4|' => [['lower' => 2, 'upper' => 4]],
            'unbounded |>=1|' => [['lower' => 1, 'upper_unbounded' => true]],
        ];
    }
}
