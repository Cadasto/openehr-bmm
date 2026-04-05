<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmSchema;
use PHPUnit\Framework\TestCase;

/**
 * Tests for BmmSchema navigation: getClassPackageQName, getClassPackagePath, getClassSubpackageSegment.
 */
final class BmmSchemaNavigationTest extends TestCase
{
    private BmmSchema $schema;

    protected function setUp(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $json = file_get_contents($path);
        self::assertIsString($json);
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true);
        $this->schema = BmmSchema::fromArray($data);
    }

    public function testGetClassPackageQNameFindsNestedClass(): void
    {
        // HIER_OBJECT_ID is in org.openehr.base.base_types.identification
        $qname = $this->schema->getClassPackageQName('HIER_OBJECT_ID');

        self::assertNotNull($qname);
        self::assertSame(
            'openehr_base_1.3.0.org.openehr.base.base_types.identification',
            $qname,
        );
    }

    public function testGetClassPackageQNameFindsTopLevelClass(): void
    {
        // Any is directly in org.openehr.base.foundation_types
        $qname = $this->schema->getClassPackageQName('Any');

        self::assertNotNull($qname);
        self::assertStringEndsWith('foundation_types', $qname);
    }

    public function testGetClassPackageQNameReturnsNullForUnknown(): void
    {
        self::assertNull($this->schema->getClassPackageQName('NonExistent'));
    }

    public function testGetClassPackagePathFindsClass(): void
    {
        $path = $this->schema->getClassPackagePath('HIER_OBJECT_ID');

        self::assertSame('org.openehr.base.base_types.identification', $path);
    }

    public function testGetClassPackagePathReturnsNullForUnknown(): void
    {
        self::assertNull($this->schema->getClassPackagePath('NonExistent'));
    }

    public function testGetClassSubpackageSegmentFindsSegment(): void
    {
        // Path is org.openehr.base.base_types.identification
        // Prefix is org.openehr.base.
        // First segment after prefix: base_types
        $segment = $this->schema->getClassSubpackageSegment('HIER_OBJECT_ID');

        self::assertSame('base_types', $segment);
    }

    public function testGetClassSubpackageSegmentReturnsNullForUnknown(): void
    {
        self::assertNull($this->schema->getClassSubpackageSegment('NonExistent'));
    }
}
