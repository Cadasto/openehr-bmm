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

    /**
     * @return array<string, mixed>
     */
    private function minimalSchemaData(): array
    {
        return [
            'bmm_version' => '2.4',
            'rm_publisher' => 'openehr',
            'schema_name' => 'test',
            'rm_release' => '1.0.0',
            'schema_revision' => '1.0.0.1',
            'schema_lifecycle_state' => 'stable',
            'schema_description' => 'test schema',
            'schema_author' => 'A. Author',
            'packages' => [
                'org.openehr.test' => ['name' => 'org.openehr.test', 'classes' => ['ITEM', 'ENTRY']],
            ],
        ];
    }

    /**
     * Optional header attributes that P_BMM_SCHEMA inherits from BMM_SCHEMA_CORE.
     */
    public function testOptionalHeaderAttributesRoundTrip(): void
    {
        $minimal = $this->minimalSchemaData();
        $data = [
            ...array_slice($minimal, 0, 8),
            'schema_contributors' => ['B. Contributor', 'C. Contributor'],
            'archetype_parent_class' => 'PATHABLE',
            'archetype_data_value_parent_class' => 'DATA_VALUE',
            'archetype_rm_closure_packages' => ['org.openehr.test.ehr'],
            'archetype_visualise_descendants_of' => 'COMPOSITION',
            'packages' => $minimal['packages'],
            'class_definitions' => ['ITEM' => ['name' => 'ITEM']],
        ];

        self::assertSame($data, BmmSchema::fromArray($data)->toArray());
    }

    /**
     * `model_name` names the archetype RM closure, e.g. "EHR" in "openEHR-EHR-...". The spec's header example uses it
     * and Archie reads it, but no P_BMM class declares it.
     */
    public function testModelNameRoundTrips(): void
    {
        $minimal = $this->minimalSchemaData();
        $data = [
            ...array_slice($minimal, 0, 4),
            'model_name' => 'EHR',
            ...array_slice($minimal, 4),
        ];

        $schema = BmmSchema::fromArray($data);

        self::assertSame('EHR', $schema->modelName);
        self::assertSame($data, $schema->toArray());
    }

    /**
     * Archie writes `archetype_visualize_descendants_of` (Jackson SNAKE_CASE) and accepts the spec's spelling only as
     * an alias. Read either; write the spec's.
     */
    public function testArchieSpellingOfVisualiseDescendantsOfIsRead(): void
    {
        $data = $this->minimalSchemaData() + ['archetype_visualize_descendants_of' => 'COMPOSITION'];

        $schema = BmmSchema::fromArray($data);

        self::assertSame('COMPOSITION', $schema->archetypeVisualiseDescendantsOf);
        self::assertSame('COMPOSITION', $schema->toArray()['archetype_visualise_descendants_of']);
    }

    /**
     * The P_BMM class model types class_definitions as List<P_BMM_CLASS>, while the spec text, its examples and
     * the published schemas key it by class name. Either shape is read; the keyed form is written.
     */
    public function testClassDefinitionsListIsWrittenKeyedByName(): void
    {
        $data = $this->minimalSchemaData() + [
            'class_definitions' => [['name' => 'ITEM'], ['name' => 'ENTRY']],
        ];

        $result = BmmSchema::fromArray($data)->toArray();

        self::assertSame(['ITEM' => ['name' => 'ITEM'], 'ENTRY' => ['name' => 'ENTRY']], $result['class_definitions']);
    }
}
