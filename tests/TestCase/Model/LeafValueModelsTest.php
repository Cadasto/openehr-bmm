<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmConstant;
use Cadasto\OpenEHR\BMM\Model\BmmGenericParameter;
use Cadasto\OpenEHR\BMM\Model\BmmSchemaInclude;
use PHPUnit\Framework\TestCase;

final class LeafValueModelsTest extends TestCase
{
    public function testBmmSchemaIncludeFromArrayToArrayAndJsonSerialize(): void
    {
        $inc = BmmSchemaInclude::fromArray(['id' => 'openehr_base_1.3.0']);

        self::assertSame('openehr_base_1.3.0', $inc->id);
        self::assertSame('openehr_base_1.3.0', $inc->getName());
        self::assertNull($inc->getAlias());

        $arr = $inc->toArray();
        self::assertSame(['id' => 'openehr_base_1.3.0'], $arr);
        self::assertSame($arr, $inc->jsonSerialize());
    }

    public function testBmmGenericParameterFromArrayToArrayAndJsonSerialize(): void
    {
        $p = BmmGenericParameter::fromArray([
            'name' => 'T',
            'conforms_to_type' => 'Ordered',
        ]);

        self::assertSame('T', $p->name);
        self::assertSame('Ordered', $p->conformsToType);

        $arr = $p->toArray();
        self::assertSame('T', $arr['name']);
        self::assertSame('Ordered', $arr['conforms_to_type']);
        self::assertSame($arr, $p->jsonSerialize());
    }

    public function testBmmConstantFromArrayToArrayAndJsonSerialize(): void
    {
        $c = BmmConstant::fromArray([
            'name' => 'PI',
            'type' => 'Real',
            'documentation' => 'Pi',
            'value' => 3.14,
        ]);

        self::assertSame('PI', $c->name);
        $arr = $c->toArray();
        self::assertSame('PI', $arr['name']);
        self::assertSame(3.14, $arr['value']);
        self::assertSame($arr, $c->jsonSerialize());
    }
}
