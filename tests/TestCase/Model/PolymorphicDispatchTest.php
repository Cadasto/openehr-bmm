<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\AbstractBmmFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\AbstractBmmProperty;
use Cadasto\OpenEHR\BMM\Model\AbstractBmmType;
use Cadasto\OpenEHR\BMM\Model\BmmContainerFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmContainerProperty;
use Cadasto\OpenEHR\BMM\Model\BmmContainerType;
use Cadasto\OpenEHR\BMM\Model\BmmGenericFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmGenericProperty;
use Cadasto\OpenEHR\BMM\Model\BmmGenericType;
use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use Cadasto\OpenEHR\BMM\Model\BmmSingleFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmSingleFunctionParameterOpen;
use Cadasto\OpenEHR\BMM\Model\BmmSingleProperty;
use Cadasto\OpenEHR\BMM\Model\BmmSinglePropertyOpen;
use PHPUnit\Framework\TestCase;

/**
 * Tests for polymorphic fromArray() dispatchers on abstract classes.
 */
final class PolymorphicDispatchTest extends TestCase
{
    // --- AbstractBmmFunctionParameter dispatch ---

    public function testDispatchSingleFunctionParameter(): void
    {
        $param = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER',
            'name' => 'a_value',
            'type' => 'String',
        ]);

        self::assertInstanceOf(BmmSingleFunctionParameter::class, $param);
        self::assertSame('a_value', $param->name);
    }

    public function testDispatchSingleFunctionParameterOpen(): void
    {
        $param = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN',
            'name' => 'a_value',
            'type' => 'T',
        ]);

        self::assertInstanceOf(BmmSingleFunctionParameterOpen::class, $param);
        self::assertSame('T', $param->type);
    }

    public function testDispatchContainerFunctionParameter(): void
    {
        $param = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_CONTAINER_FUNCTION_PARAMETER',
            'name' => 'items',
            'type_def' => [
                '_type' => 'P_BMM_CONTAINER_TYPE',
                'container_type' => 'List',
                'type' => 'String',
            ],
        ]);

        self::assertInstanceOf(BmmContainerFunctionParameter::class, $param);
        self::assertSame('items', $param->name);
    }

    public function testDispatchGenericFunctionParameter(): void
    {
        $param = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_GENERIC_FUNCTION_PARAMETER',
            'name' => 'interval',
            'type_def' => [
                '_type' => 'P_BMM_GENERIC_TYPE',
                'root_type' => 'Interval',
                'generic_parameters' => ['T'],
            ],
        ]);

        self::assertInstanceOf(BmmGenericFunctionParameter::class, $param);
        self::assertSame('interval', $param->name);
    }

    public function testDispatchDefaultsFunctionParameter(): void
    {
        // No _type defaults to P_BMM_SINGLE_FUNCTION_PARAMETER
        $param = AbstractBmmFunctionParameter::fromArray([
            'name' => 'x',
            'type' => 'Integer',
        ]);

        self::assertInstanceOf(BmmSingleFunctionParameter::class, $param);
    }

    // --- AbstractBmmProperty dispatch ---

    public function testDispatchSinglePropertyOpen(): void
    {
        $prop = AbstractBmmProperty::fromArray([
            '_type' => 'P_BMM_SINGLE_PROPERTY_OPEN',
            'name' => 'value',
            'type' => 'T',
        ]);

        self::assertInstanceOf(BmmSinglePropertyOpen::class, $prop);
        self::assertSame('T', $prop->type);
    }

    // --- AbstractBmmType dispatch ---

    public function testDispatchContainerType(): void
    {
        $type = AbstractBmmType::fromArray([
            '_type' => 'P_BMM_CONTAINER_TYPE',
            'container_type' => 'List',
            'type' => 'String',
        ]);

        self::assertInstanceOf(BmmContainerType::class, $type);
    }

    public function testDispatchGenericType(): void
    {
        $type = AbstractBmmType::fromArray([
            '_type' => 'P_BMM_GENERIC_TYPE',
            'root_type' => 'Interval',
            'generic_parameters' => ['T'],
        ]);

        self::assertInstanceOf(BmmGenericType::class, $type);
    }

    public function testDispatchDefaultsToSimpleType(): void
    {
        $type = AbstractBmmType::fromArray([
            'type' => 'Boolean',
        ]);

        self::assertInstanceOf(BmmSimpleType::class, $type);
    }
}
