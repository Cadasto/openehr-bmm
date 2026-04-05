<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmContainerType;
use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use PHPUnit\Framework\TestCase;

final class BmmContainerTypeTest extends TestCase
{
    public function testToArrayNestedSimpleType(): void
    {
        $inner = new BmmSimpleType('String');
        $ct = new BmmContainerType('List', 'String', $inner);

        $arr = $ct->toArray();

        self::assertSame('P_BMM_CONTAINER_TYPE', $arr['_type']);
        self::assertSame('List', $arr['container_type']);
        self::assertSame('String', $arr['type']);
        self::assertSame(['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'String'], $arr['type_def']);
        self::assertSame($arr, $ct->jsonSerialize());
    }

    public function testFromArrayRoundTrip(): void
    {
        $data = [
            '_type' => 'P_BMM_CONTAINER_TYPE',
            'container_type' => 'Array',
            'type' => 'Integer',
            'type_def' => [
                '_type' => 'P_BMM_SIMPLE_TYPE',
                'type' => 'Integer',
            ],
        ];

        $ct = BmmContainerType::fromArray($data);

        self::assertSame('Array', $ct->containerType);
        self::assertSame('Integer', $ct->type);
        self::assertInstanceOf(BmmSimpleType::class, $ct->typeDef);
        self::assertSame('Integer', $ct->typeDef->type);
    }
}
