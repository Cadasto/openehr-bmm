<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmSchema;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BmmSchemaEdgeCaseTest extends TestCase
{
    public function testFromArrayWithoutPackagesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Schema must contain at least one package');

        BmmSchema::fromArray([
            'bmm_version' => '2.4',
            'rm_publisher' => 'x',
            'schema_name' => 'y',
            'rm_release' => '1',
            'schema_revision' => '1',
            'schema_lifecycle_state' => 'stable',
            'schema_description' => 'd',
            'schema_author' => 'a',
            'packages' => [],
        ]);
    }

    public function testGetSchemaIdAndAccessors(): void
    {
        $schema = BmmSchema::fromArray([
            'bmm_version' => '2.4',
            'rm_publisher' => 'openehr',
            'schema_name' => 'base',
            'rm_release' => '1.3.0',
            'schema_revision' => '1.3.0.2',
            'schema_lifecycle_state' => 'stable',
            'schema_description' => 'desc',
            'schema_author' => 'author',
            'packages' => [
                'p' => [
                    'name' => 'p',
                    'packages' => [],
                    'classes' => [],
                ],
            ],
        ]);

        self::assertSame('openehr_base_1.3.0', $schema->getSchemaId());
        self::assertSame('openehr_base_1.3.0', $schema->getName());
        self::assertSame('base', $schema->getAlias());
    }
}
