<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use PHPUnit\Framework\TestCase;

final class BmmSimpleTypeTest extends TestCase
{
    public function testToArrayReturnsExpectedStructure(): void
    {
        $type = new BmmSimpleType(type: 'String');

        $result = $type->toArray();

        self::assertSame([
            '_type' => 'P_BMM_SIMPLE_TYPE',
            'type' => 'String',
        ], $result);
    }

    public function testJsonSerializeDelegatesToToArray(): void
    {
        $type = new BmmSimpleType(type: 'Integer');

        self::assertSame($type->toArray(), $type->jsonSerialize());
    }

    public function testFromArrayToArrayRoundTrip(): void
    {
        $data = ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'Boolean'];

        $type = BmmSimpleType::fromArray($data);
        $result = $type->toArray();

        self::assertSame('P_BMM_SIMPLE_TYPE', $result['_type']);
        self::assertSame('Boolean', $result['type']);
    }
}
