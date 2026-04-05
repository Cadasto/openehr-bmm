<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\AbstractBmmFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\AbstractBmmProperty;
use Cadasto\OpenEHR\BMM\Model\BmmContainerFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmContainerProperty;
use Cadasto\OpenEHR\BMM\Model\BmmGenericFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmGenericProperty;
use Cadasto\OpenEHR\BMM\Model\BmmSingleFunctionParameter;
use Cadasto\OpenEHR\BMM\Model\BmmSingleFunctionParameterOpen;
use Cadasto\OpenEHR\BMM\Model\BmmSingleProperty;
use Cadasto\OpenEHR\BMM\Model\BmmSinglePropertyOpen;
use PHPUnit\Framework\TestCase;

final class PropertyAndParameterFromArrayTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function listTypeDef(): array
    {
        return [
            '_type' => 'P_BMM_CONTAINER_TYPE',
            'container_type' => 'List',
            'type' => 'String',
            'type_def' => [
                '_type' => 'P_BMM_SIMPLE_TYPE',
                'type' => 'String',
            ],
        ];
    }

    public function testAbstractBmmPropertyDispatchesSingle(): void
    {
        $p = AbstractBmmProperty::fromArray([
            '_type' => 'P_BMM_SINGLE_PROPERTY',
            'name' => 'x',
            'type' => 'String',
        ]);
        self::assertInstanceOf(BmmSingleProperty::class, $p);
        self::assertSame($p->toArray(), $p->jsonSerialize());
    }

    public function testAbstractBmmPropertyDispatchesSingleOpen(): void
    {
        $p = AbstractBmmProperty::fromArray([
            '_type' => 'P_BMM_SINGLE_PROPERTY_OPEN',
            'name' => 'data',
            'type' => 'T',
        ]);
        self::assertInstanceOf(BmmSinglePropertyOpen::class, $p);
    }

    public function testAbstractBmmPropertyDispatchesContainer(): void
    {
        $p = AbstractBmmProperty::fromArray([
            '_type' => 'P_BMM_CONTAINER_PROPERTY',
            'name' => 'items',
            'type_def' => self::listTypeDef(),
        ]);
        self::assertInstanceOf(BmmContainerProperty::class, $p);
    }

    public function testAbstractBmmPropertyDispatchesGeneric(): void
    {
        $p = AbstractBmmProperty::fromArray([
            '_type' => 'P_BMM_GENERIC_PROPERTY',
            'name' => 'values',
            'type_def' => [
                '_type' => 'P_BMM_GENERIC_TYPE',
                'root_type' => 'List',
                'generic_parameters' => ['T'],
            ],
        ]);
        self::assertInstanceOf(BmmGenericProperty::class, $p);
    }

    public function testAbstractBmmPropertyDefaultTypeIsSingle(): void
    {
        $p = AbstractBmmProperty::fromArray([
            'name' => 'only',
            'type' => 'Any',
        ]);
        self::assertInstanceOf(BmmSingleProperty::class, $p);
    }

    public function testAbstractBmmFunctionParameterDispatchesSingle(): void
    {
        $p = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER',
            'name' => 'a',
            'type' => 'Integer',
        ]);
        self::assertInstanceOf(BmmSingleFunctionParameter::class, $p);
        self::assertSame($p->toArray(), $p->jsonSerialize());
    }

    public function testAbstractBmmFunctionParameterDispatchesSingleOpen(): void
    {
        $p = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN',
            'name' => 'key',
            'type' => 'K',
        ]);
        self::assertInstanceOf(BmmSingleFunctionParameterOpen::class, $p);
    }

    public function testAbstractBmmFunctionParameterDispatchesContainer(): void
    {
        $p = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_CONTAINER_FUNCTION_PARAMETER',
            'name' => 'items',
            'type_def' => self::listTypeDef(),
        ]);
        self::assertInstanceOf(BmmContainerFunctionParameter::class, $p);
    }

    public function testAbstractBmmFunctionParameterDispatchesGeneric(): void
    {
        $p = AbstractBmmFunctionParameter::fromArray([
            '_type' => 'P_BMM_GENERIC_FUNCTION_PARAMETER',
            'name' => 'g',
            'type_def' => [
                '_type' => 'P_BMM_GENERIC_TYPE',
                'root_type' => 'List',
                'generic_parameters' => ['T'],
            ],
        ]);
        self::assertInstanceOf(BmmGenericFunctionParameter::class, $p);
    }
}
