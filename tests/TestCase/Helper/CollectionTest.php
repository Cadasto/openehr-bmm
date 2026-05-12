<?php

declare(strict_types=1);

namespace Tests\TestCase\Helper;

use Cadasto\OpenEHR\BMM\Helper\Collection;
use Cadasto\OpenEHR\BMM\Model\BmmSchemaInclude;
use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testAddGetAndAlias(): void
    {
        $c = new Collection();
        $a = new BmmSchemaInclude('inc_a');
        $b = new BmmSchemaInclude('inc_b');
        $c->add($a);
        $c->add($b, 'alias_b');

        self::assertSame($a, $c->get('inc_a'));
        self::assertSame($b, $c->get('inc_b'));
        self::assertSame($b, $c->get('alias_b'));
    }

    public function testSetStoresUnderDocumentKeyNotGetName(): void
    {
        $c = new Collection();
        $c->set('K', new BmmSimpleType('String'));

        self::assertSame('String', $c->get('K')?->getName());
        self::assertNull($c->get('String'));
    }

    public function testFlushClearsItemsAndAliases(): void
    {
        $c = new Collection();
        $c->add(new BmmSchemaInclude('x'), 'y');
        $c->flush();

        self::assertCount(0, $c);
        self::assertNull($c->get('x'));
        self::assertNull($c->get('y'));
    }

    public function testToArrayMapsItemsToArrays(): void
    {
        $c = new Collection();
        $c->add(new BmmSchemaInclude('id1'));

        $out = $c->toArray();

        self::assertSame(['id' => 'id1'], $out['id1']);
    }

    public function testJsonSerializeDelegatesToToArray(): void
    {
        $c = new Collection();
        $c->add(new BmmSchemaInclude('only'));

        self::assertSame($c->toArray(), $c->jsonSerialize());
    }

    public function testPopulateFromUsesAddByDefault(): void
    {
        $c = new Collection();
        $c->populateFrom(
            [['id' => 'a'], ['id' => 'b']],
            static fn(array $d): BmmSchemaInclude => new BmmSchemaInclude($d['id']),
        );

        self::assertSame('a', $c->get('a')?->getName());
        self::assertSame('b', $c->get('b')?->getName());
    }

    public function testPopulateFromKeyedPreservesSourceKeys(): void
    {
        $c = new Collection();
        $c->populateFrom(
            ['K' => 'String', 'V' => 'Integer'],
            static fn(string $type): BmmSimpleType => new BmmSimpleType($type),
            keyed: true,
        );

        self::assertSame('String', $c->get('K')?->getName());
        self::assertSame('Integer', $c->get('V')?->getName());
        self::assertNull($c->get('String'));
    }

    public function testPopulateFromIsNoopForEmptyOrNonIterableInput(): void
    {
        $c = new Collection();
        $factory = static fn(mixed $d): BmmSchemaInclude => new BmmSchemaInclude((string) $d);

        $c->populateFrom([], $factory);
        $c->populateFrom(null, $factory);
        $c->populateFrom('not iterable', $factory);

        self::assertCount(0, $c);
    }
}
