<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmContainerFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmGenericFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmSingleFunctionParameterOpen;
use PHPUnit\Framework\TestCase;

/**
 * Tests for function parameter fromArray/toArray round-trips.
 */
final class FunctionParameterTest extends TestCase
{
    public function testContainerFunctionParameterRoundTrip(): void
    {
        $data = [
            '_type' => 'P_BMM_CONTAINER_FUNCTION_PARAMETER',
            'name' => 'items',
            'documentation' => 'A list of items.',
            'type_def' => [
                'container_type' => 'List',
                'type' => 'String',
            ],
            'cardinality' => [
                'lower' => 1,
                'upper' => 10,
            ],
        ];

        $param = BmmContainerFunctionParameter::fromArray($data);

        self::assertSame('items', $param->name);
        self::assertSame('items', $param->getName());
        self::assertSame('A list of items.', $param->documentation);
        self::assertSame('List', $param->typeDef->containerType);
        self::assertNotNull($param->cardinality);
        self::assertSame(1, $param->cardinality->lower);

        $arr = $param->toArray();
        self::assertSame('P_BMM_CONTAINER_FUNCTION_PARAMETER', $arr['_type']);
        self::assertArrayHasKey('type_def', $arr);
        self::assertArrayHasKey('cardinality', $arr);
    }

    public function testContainerFunctionParameterWithoutCardinality(): void
    {
        $param = BmmContainerFunctionParameter::fromArray([
            'name' => 'values',
            'type_def' => [
                'container_type' => 'Set',
                'type' => 'Integer',
            ],
        ]);

        // Default cardinality is |0..*|
        self::assertNotNull($param->cardinality);
        self::assertSame(0, $param->cardinality->lower);
    }

    public function testGenericFunctionParameterRoundTrip(): void
    {
        $param = BmmGenericFunctionParameter::fromArray([
            'name' => 'pair',
            'type_def' => [
                '_type' => 'P_BMM_GENERIC_TYPE',
                'root_type' => 'Hash',
                'generic_parameters' => ['K', 'V'],
            ],
            'is_nullable' => true,
        ]);

        self::assertSame('pair', $param->name);
        self::assertTrue($param->isNullable);
        self::assertSame('Hash', $param->typeDef->rootType);

        $arr = $param->toArray();
        self::assertSame('P_BMM_GENERIC_FUNCTION_PARAMETER', $arr['_type']);
        self::assertArrayHasKey('type_def', $arr);
    }

    public function testSingleFunctionParameterOpenRoundTrip(): void
    {
        $param = BmmSingleFunctionParameterOpen::fromArray([
            'name' => 'other',
            'type' => 'T',
            'documentation' => 'Open type param.',
        ]);

        self::assertSame('other', $param->name);
        self::assertSame('T', $param->type);

        $arr = $param->toArray();
        self::assertSame('P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN', $arr['_type']);
        self::assertSame('T', $arr['type']);
    }
}
