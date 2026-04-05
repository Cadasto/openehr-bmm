<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\BmmEnumerationInteger;
use Cadasto\OpenEHR\BMM\Model\BmmEnumerationString;
use Cadasto\OpenEHR\BMM\Model\BmmFunction;
use Cadasto\OpenEHR\BMM\Model\BmmInterface;
use Cadasto\OpenEHR\BMM\Model\BmmPackage;
use PHPUnit\Framework\TestCase;

final class CompositeModelsTest extends TestCase
{
    public function testBmmInterfaceFromArrayToArray(): void
    {
        $iface = BmmInterface::fromArray([
            '_type' => 'P_BMM_INTERFACE',
            'name' => 'Comparable',
            'documentation' => 'Comparable types.',
        ]);

        self::assertSame('Comparable', $iface->name);
        $arr = $iface->toArray();
        self::assertSame('P_BMM_INTERFACE', $arr['_type']);
        self::assertSame($arr, $iface->jsonSerialize());
    }

    public function testBmmEnumerationStringFromArrayToArray(): void
    {
        $e = BmmEnumerationString::fromArray([
            '_type' => 'P_BMM_ENUMERATION_STRING',
            'name' => 'VALIDITY_KIND',
            'item_names' => ['valid', 'invalid'],
            'item_values' => ['valid', 'invalid'],
        ]);

        self::assertSame('VALIDITY_KIND', $e->name);
        self::assertSame(['valid', 'invalid'], $e->itemNames);
        $arr = $e->toArray();
        self::assertSame('P_BMM_ENUMERATION_STRING', $arr['_type']);
        self::assertSame($arr, $e->jsonSerialize());
    }

    public function testBmmEnumerationIntegerFromArrayToArray(): void
    {
        $e = BmmEnumerationInteger::fromArray([
            '_type' => 'P_BMM_ENUMERATION_INTEGER',
            'name' => 'SMALL_INT',
            'item_values' => [1, 2],
        ]);

        self::assertSame([1, 2], $e->itemValues);
        self::assertSame($e->toArray(), $e->jsonSerialize());
    }

    public function testBmmPackageNestedFromArray(): void
    {
        $pkg = BmmPackage::fromArray([
            'name' => 'root',
            'packages' => [
                'child' => [
                    'name' => 'child',
                    'packages' => [],
                    'classes' => ['A', 'B'],
                ],
            ],
            'classes' => ['RootClass'],
        ]);

        self::assertSame('root', $pkg->name);
        $child = $pkg->packages->get('child');
        self::assertInstanceOf(BmmPackage::class, $child);
        self::assertSame(['A', 'B'], $child->classes);
        self::assertSame(['RootClass', 'A', 'B'], $pkg->getAllClassNames());
        self::assertSame($pkg->toArray(), $pkg->jsonSerialize());
    }

    public function testBmmEnumerationIntegerWithFunctions(): void
    {
        $e = BmmEnumerationInteger::fromArray([
            '_type' => 'P_BMM_ENUMERATION_INTEGER',
            'name' => 'PRIORITY',
            'documentation' => 'Priority levels.',
            'ancestors' => ['Integer'],
            'item_names' => ['low', 'high'],
            'item_values' => [0, 1],
            'item_documentations' => ['Low priority', 'High priority'],
            'functions' => [
                'as_string' => [
                    'name' => 'as_string',
                    'result' => ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'String'],
                ],
            ],
        ]);

        self::assertSame('PRIORITY', $e->name);
        self::assertCount(1, $e->functions);
        self::assertSame(['low', 'high'], $e->itemNames);
        self::assertSame(['Low priority', 'High priority'], $e->itemDocumentations);

        $arr = $e->toArray();
        self::assertSame('P_BMM_ENUMERATION_INTEGER', $arr['_type']);
        self::assertArrayHasKey('functions', $arr);
    }

    public function testBmmFunctionMinimalFromArray(): void
    {
        $fn = BmmFunction::fromArray([
            'name' => 'is_equal',
            'documentation' => 'Equality test.',
        ]);

        self::assertSame('is_equal', $fn->name);
        self::assertSame($fn->toArray(), $fn->jsonSerialize());
    }

    public function testBmmFunctionWithParametersAndResult(): void
    {
        $fn = BmmFunction::fromArray([
            'name' => 'has',
            'documentation' => 'Membership test.',
            'is_abstract' => true,
            'parameters' => [
                'a_value' => [
                    '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER',
                    'name' => 'a_value',
                    'type' => 'T',
                ],
            ],
            'result' => ['_type' => 'P_BMM_SIMPLE_TYPE', 'type' => 'Boolean'],
            'is_nullable' => false,
            'pre_conditions' => ['Pre' => 'not empty'],
            'post_conditions' => ['Post' => 'result valid'],
        ]);

        self::assertTrue($fn->isAbstract);
        self::assertCount(1, $fn->parameters);
        self::assertNotNull($fn->result);
        self::assertSame(['Pre' => 'not empty'], $fn->preConditions);

        $arr = $fn->toArray();
        self::assertArrayHasKey('parameters', $arr);
        self::assertArrayHasKey('result', $arr);
    }

    public function testBmmPackageGetClassPackageQName(): void
    {
        $pkg = BmmPackage::fromArray([
            'name' => 'root',
            'packages' => [
                'child' => [
                    'name' => 'child',
                    'classes' => ['Foo'],
                ],
            ],
            'classes' => ['Bar'],
        ]);

        self::assertSame('root', $pkg->getClassPackageQName('Bar'));
        self::assertSame('root.child', $pkg->getClassPackageQName('Foo'));
        self::assertNull($pkg->getClassPackageQName('Missing'));
    }
}
