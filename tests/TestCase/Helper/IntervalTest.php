<?php

declare(strict_types=1);

namespace Tests\TestCase\Helper;

use Cadasto\OpenEHR\BMM\Helper\Interval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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
     */
    #[DataProvider('upperUnboundedProvider')]
    public function testFromArrayUpperUnbounded(array $data, bool $expected): void
    {
        $interval = Interval::fromArray($data);

        self::assertSame($expected, $interval->upperUnbounded);
    }

    /**
     * @return array<string, array{array<string, mixed>, bool}>
     */
    public static function upperUnboundedProvider(): array
    {
        return [
            'upper given, flag omitted' => [['lower' => 2, 'upper' => 4], false],
            'upper null, flag omitted' => [['lower' => 1, 'upper' => null], true],
            'upper omitted, flag omitted' => [['lower' => 1], true],
            'empty input' => [[], true],
            'upper given, flag true' => [['lower' => 2, 'upper' => 4, 'upper_unbounded' => true], true],
            'upper omitted, flag false' => [['lower' => 1, 'upper_unbounded' => false], false],
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
