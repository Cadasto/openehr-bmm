<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;
use Cadasto\OpenEHR\BMM\Model\BmmGenericType;
use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use PHPUnit\Framework\TestCase;

final class BmmGenericTypeTest extends TestCase
{
    public function testToArrayWithNestedDefs(): void
    {
        $col = new Collection();
        // Formal parameter name T must be preserved; nested type’s getName() is "Ordered".
        $col->set('T', new BmmSimpleType('Ordered'));
        $gt = new BmmGenericType('Interval', $col, ['T']);

        $arr = $gt->toArray();

        self::assertSame('P_BMM_GENERIC_TYPE', $arr['_type']);
        self::assertSame('Interval', $arr['root_type']);
        self::assertSame(['T'], $arr['generic_parameters']);
        self::assertArrayHasKey('T', $arr['generic_parameter_defs']);
        self::assertSame(['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'Ordered'], $arr['generic_parameter_defs']['T']);
        self::assertSame($arr, $gt->jsonSerialize());
    }

    public function testFromArrayWithStringGenericParameters(): void
    {
        $data = [
            '_type' => 'P_BMM_GENERIC_TYPE',
            'root_type' => 'List',
            'generic_parameters' => ['T'],
        ];

        $gt = BmmGenericType::fromArray($data);

        self::assertSame('List', $gt->rootType);
        self::assertSame(['T'], $gt->genericParameters);
    }

    public function testRoundTripWithNestedTypeDefInGenericParameters(): void
    {
        $data = [
            '_type' => 'P_BMM_GENERIC_TYPE',
            'root_type' => 'Map',
            'generic_parameters' => [
                ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'String'],
                ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'Integer'],
            ],
        ];

        $gt = BmmGenericType::fromArray($data);

        self::assertCount(2, $gt->genericParameters);
        self::assertInstanceOf(BmmSimpleType::class, $gt->genericParameters[0]);
        self::assertInstanceOf(BmmSimpleType::class, $gt->genericParameters[1]);

        $arr = $gt->toArray();
        // toArray must emit plain arrays for nested type-defs, not live objects.
        self::assertIsArray($arr['generic_parameters'][0]);
        self::assertIsArray($arr['generic_parameters'][1]);
        self::assertSame(
            ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'String'],
            $arr['generic_parameters'][0],
        );
        self::assertSame(
            ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'Integer'],
            $arr['generic_parameters'][1],
        );
    }

    public function testToArrayPreservesStringGenericParameters(): void
    {
        $gt = new BmmGenericType('List', new Collection(), ['T']);
        self::assertSame(['T'], $gt->toArray()['generic_parameters']);
    }
}
