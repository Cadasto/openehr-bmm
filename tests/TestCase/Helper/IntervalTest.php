<?php

declare(strict_types=1);

namespace Tests\TestCase\Helper;

use Cadasto\OpenEHR\BMM\Helper\Interval;
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
}
