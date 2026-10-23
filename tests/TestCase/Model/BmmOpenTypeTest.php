<?php

declare(strict_types=1);

namespace Tests\TestCase\Model;

use Cadasto\OpenEHR\BMM\Model\AbstractBmmType;
use Cadasto\OpenEHR\BMM\Model\BmmFunction;
use Cadasto\OpenEHR\BMM\Model\BmmOpenType;
use Cadasto\OpenEHR\BMM\Model\BmmSimpleType;
use PHPUnit\Framework\TestCase;

/**
 * P_BMM_OPEN_TYPE: a reference to an open generic parameter such as `T`.
 */
final class BmmOpenTypeTest extends TestCase
{
    public function testDispatchesToOpenType(): void
    {
        $type = AbstractBmmType::fromArray(['_type' => 'P_BMM_OPEN_TYPE', 'type' => 'T']);

        self::assertInstanceOf(BmmOpenType::class, $type);
        // Still a BmmSimpleType, so consumers that render simple types keep working.
        self::assertInstanceOf(BmmSimpleType::class, $type);
        self::assertSame('T', $type->type);
    }

    public function testRoundTripKeepsOpenTypeDiscriminator(): void
    {
        $data = ['_type' => 'P_BMM_OPEN_TYPE', 'type' => 'T'];

        self::assertSame($data, AbstractBmmType::fromArray($data)->toArray());
    }

    public function testFunctionResultKeepsOpenType(): void
    {
        $function = BmmFunction::fromArray([
            'name' => 'first',
            'result' => ['_type' => 'P_BMM_OPEN_TYPE', 'type' => 'T'],
        ]);

        self::assertSame(['_type' => 'P_BMM_OPEN_TYPE', 'type' => 'T'], $function->toArray()['result']);
    }
}
