<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmSchema;
use PHPUnit\Framework\TestCase;

final class BmmSchemaTest extends TestCase
{
    public function testToArrayReturnsExpectedKeys(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $json = file_get_contents($path);
        self::assertIsString($json);
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true);

        $schema = BmmSchema::fromArray($data);
        $result = $schema->toArray();

        self::assertSame('2.4', $result['bmm_version']);
        self::assertSame('openehr', $result['rm_publisher']);
        self::assertSame('base', $result['schema_name']);
        self::assertSame('1.3.0', $result['rm_release']);
        self::assertArrayHasKey('packages', $result);
        self::assertArrayHasKey('class_definitions', $result);
        self::assertIsArray($result['packages']);
        self::assertIsArray($result['class_definitions']);
    }

    public function testJsonSerializeDelegatesToToArray(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $json = file_get_contents($path);
        self::assertIsString($json);
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true);

        $schema = BmmSchema::fromArray($data);

        self::assertSame($schema->toArray(), $schema->jsonSerialize());
    }

    public function testToArrayOutputIsAllArrays(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $json = file_get_contents($path);
        self::assertIsString($json);
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true);

        $schema = BmmSchema::fromArray($data);
        $result = $schema->toArray();

        // Packages should be plain arrays, not Collection objects
        foreach ($result['packages'] as $package) {
            self::assertIsArray($package, 'Package should be a plain array, not an object');
        }

        // Class definitions should be plain arrays
        foreach ($result['class_definitions'] as $classDef) {
            self::assertIsArray($classDef, 'Class definition should be a plain array, not an object');
        }
    }
}
