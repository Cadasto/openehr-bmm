<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Abstract BMM class — polymorphic fromArray() dispatcher.
 */
abstract readonly class AbstractBmmClass extends AbstractBmmModel
{
    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): BmmInterface|BmmEnumerationString|BmmEnumerationInteger|BmmClass
    {
        $type = $data['_type'] ?? 'P_BMM_CLASS';
        return match ($type) {
            'P_BMM_INTERFACE' => BmmInterface::fromArray($data),
            'P_BMM_ENUMERATION_STRING' => BmmEnumerationString::fromArray($data),
            'P_BMM_ENUMERATION_INTEGER' => BmmEnumerationInteger::fromArray($data),
            default => BmmClass::fromArray($data),
        };
    }
}
