<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\AbstractBmmClass;
use Cadasto\OpenEHR\BMM\Model\BmmClass;
use Cadasto\OpenEHR\BMM\Model\BmmGenericParameter;
use Cadasto\OpenEHR\BMM\Model\BmmSingleProperty;
use PHPUnit\Framework\TestCase;

final class BmmClassTest extends TestCase
{
    public function testMinimalConstruction(): void
    {
        $class = new BmmClass(name: 'HIER_OBJECT_ID');

        self::assertSame('HIER_OBJECT_ID', $class->name);
        self::assertSame('HIER_OBJECT_ID', $class->getName());
        self::assertNull($class->getAlias());
        self::assertFalse($class->isAbstract);
        self::assertSame([], $class->ancestors);
        self::assertNull($class->documentation);
        self::assertCount(0, $class->properties);
        self::assertCount(0, $class->genericParameterDefs);
        self::assertCount(0, $class->constants);
        self::assertCount(0, $class->functions);
        self::assertSame([], $class->invariants);
    }

    public function testFromArrayMinimal(): void
    {
        $data = [
            'name' => 'HIER_OBJECT_ID',
            'documentation' => 'Concrete type corresponding to hierarchical identifiers.',
            'ancestors' => ['UID_BASED_ID'],
        ];

        $class = BmmClass::fromArray($data);

        self::assertSame('HIER_OBJECT_ID', $class->name);
        self::assertSame(
            'Concrete type corresponding to hierarchical identifiers.',
            $class->documentation,
        );
        self::assertSame(['UID_BASED_ID'], $class->ancestors);
        self::assertFalse($class->isAbstract);
        self::assertCount(0, $class->properties);
    }

    public function testFromArrayWithPropertiesAndGenerics(): void
    {
        $data = [
            'name' => 'Point_interval',
            'documentation' => 'Type representing an Interval that happens to be a point value.',
            'ancestors' => ['Interval'],
            'generic_parameter_defs' => [
                'T' => [
                    'name' => 'T',
                    'conforms_to_type' => 'Ordered',
                ],
            ],
            'properties' => [
                'lower_unbounded' => [
                    '_type' => 'P_BMM_SINGLE_PROPERTY',
                    'name' => 'lower_unbounded',
                    'documentation' => 'Lower boundary open (i.e. = -infinity).',
                    'is_mandatory' => true,
                    'type' => 'Boolean',
                    'default' => false,
                ],
                'upper_unbounded' => [
                    '_type' => 'P_BMM_SINGLE_PROPERTY',
                    'name' => 'upper_unbounded',
                    'is_mandatory' => true,
                    'type' => 'Boolean',
                    'default' => false,
                ],
            ],
            'invariants' => [
                'Inv_point' => 'lower = upper',
            ],
        ];

        $class = BmmClass::fromArray($data);

        self::assertSame('Point_interval', $class->name);
        self::assertSame(['Interval'], $class->ancestors);

        // Generic parameter defs
        self::assertCount(1, $class->genericParameterDefs);
        $genericT = $class->genericParameterDefs->get('T');
        self::assertInstanceOf(BmmGenericParameter::class, $genericT);
        self::assertSame('T', $genericT->name);
        self::assertSame('Ordered', $genericT->conformsToType);

        // Properties
        self::assertCount(2, $class->properties);
        $lowerUnbounded = $class->properties->get('lower_unbounded');
        self::assertInstanceOf(BmmSingleProperty::class, $lowerUnbounded);
        self::assertSame('Boolean', $lowerUnbounded->type);
        self::assertTrue($lowerUnbounded->isMandatory);
        self::assertFalse($lowerUnbounded->default);

        // Invariants
        self::assertSame(['Inv_point' => 'lower = upper'], $class->invariants);
    }

    public function testAbstractBmmClassFromArrayDispatchesCorrectly(): void
    {
        $data = [
            '_type' => 'P_BMM_CLASS',
            'name' => 'ANY',
            'is_abstract' => true,
        ];

        $class = AbstractBmmClass::fromArray($data);

        self::assertInstanceOf(BmmClass::class, $class);
        self::assertSame('ANY', $class->name);
        self::assertTrue($class->isAbstract);
    }

    public function testJsonSerializeRoundTrip(): void
    {
        $data = [
            'name' => 'OBJECT_REF',
            'documentation' => 'A reference to an object.',
            'ancestors' => ['Any'],
            'properties' => [
                'namespace' => [
                    '_type' => 'P_BMM_SINGLE_PROPERTY',
                    'name' => 'namespace',
                    'type' => 'String',
                ],
                'type' => [
                    '_type' => 'P_BMM_SINGLE_PROPERTY',
                    'name' => 'type',
                    'is_mandatory' => true,
                    'type' => 'String',
                ],
            ],
        ];

        $class = BmmClass::fromArray($data);
        $serialized = $class->jsonSerialize();

        self::assertSame('OBJECT_REF', $serialized['name']);
        self::assertSame('A reference to an object.', $serialized['documentation']);
        self::assertSame(['Any'], $serialized['ancestors']);
        self::assertArrayHasKey('namespace', $serialized['properties']);
        self::assertArrayHasKey('type', $serialized['properties']);

        // toArray() produces the same result
        self::assertSame($serialized, $class->toArray());
    }

    public function testFromArrayWithRealFixture(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $json = file_get_contents($path);
        self::assertIsString($json);
        /** @var array<string, mixed> $schema */
        $schema = json_decode($json, true);

        /** @var array<string, mixed> $pointInterval */
        $pointInterval = $schema['class_definitions']['Point_interval'];
        $class = BmmClass::fromArray($pointInterval);

        self::assertSame('Point_interval', $class->name);
        self::assertSame(['Interval'], $class->ancestors);
        self::assertCount(1, $class->genericParameterDefs);
        self::assertCount(4, $class->properties);
        self::assertCount(1, $class->invariants);
        self::assertSame('lower = upper', $class->invariants['Inv_point']);
    }
}
