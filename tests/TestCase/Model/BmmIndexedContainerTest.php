<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\AbstractBmmProperty;
use Cadasto\OpenEHR\BMM\Model\AbstractBmmType;
use Cadasto\OpenEHR\BMM\Model\BmmContainerProperty;
use Cadasto\OpenEHR\BMM\Model\BmmContainerType;
use Cadasto\OpenEHR\BMM\Model\BmmIndexedContainerProperty;
use Cadasto\OpenEHR\BMM\Model\BmmIndexedContainerType;
use PHPUnit\Framework\TestCase;

/**
 * P_BMM_INDEXED_CONTAINER_PROPERTY / P_BMM_INDEXED_CONTAINER_TYPE, e.g. `custom_actions: Hash<String, EVENT_ACTION>`
 * from the P_BMM specification.
 */
final class BmmIndexedContainerTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function hashPropertyData(): array
    {
        return [
            '_type' => 'P_BMM_INDEXED_CONTAINER_PROPERTY',
            'name' => 'custom_actions',
            'type_def' => [
                'container_type' => 'Hash',
                'index_type' => 'String',
                'type' => 'EVENT_ACTION',
            ],
            'cardinality' => ['lower' => 0, 'upper_unbounded' => true],
        ];
    }

    public function testPropertyDispatchesToIndexedContainerProperty(): void
    {
        $property = AbstractBmmProperty::fromArray($this->hashPropertyData());

        self::assertInstanceOf(BmmIndexedContainerProperty::class, $property);
        // Still a container property, so consumers that branch on BmmContainerProperty keep working.
        self::assertInstanceOf(BmmContainerProperty::class, $property);
        self::assertInstanceOf(BmmIndexedContainerType::class, $property->typeDef);
        self::assertSame('String', $property->typeDef->indexType);
        self::assertSame('EVENT_ACTION', $property->typeDef->type);
    }

    public function testPropertyRoundTrips(): void
    {
        $data = $this->hashPropertyData();

        self::assertSame($data, AbstractBmmProperty::fromArray($data)->toArray());
    }

    public function testTypeDispatchesToIndexedContainerType(): void
    {
        $type = AbstractBmmType::fromArray([
            '_type' => 'P_BMM_INDEXED_CONTAINER_TYPE',
            'container_type' => 'Hash',
            'index_type' => 'String',
            'type' => 'EVENT_ACTION',
        ]);

        self::assertInstanceOf(BmmIndexedContainerType::class, $type);
        self::assertInstanceOf(BmmContainerType::class, $type);
        self::assertSame('String', $type->indexType);
    }

    public function testTypeRoundTrips(): void
    {
        $data = [
            '_type' => 'P_BMM_INDEXED_CONTAINER_TYPE',
            'container_type' => 'Hash',
            'index_type' => 'String',
            'type' => 'EVENT_ACTION',
        ];

        self::assertSame($data, AbstractBmmType::fromArray($data)->toArray());
    }
}
