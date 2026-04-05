<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Abstract BMM type — polymorphic fromArray() dispatcher.
 */
abstract readonly class AbstractBmmType extends AbstractBmmModel
{
    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): BmmContainerType|BmmGenericType|BmmSimpleType
    {
        $type = $data['_type'] ?? 'P_BMM_SIMPLE_TYPE';
        return match ($type) {
            'P_BMM_CONTAINER_TYPE' => BmmContainerType::fromArray($data),
            'P_BMM_GENERIC_TYPE' => BmmGenericType::fromArray($data),
            default => BmmSimpleType::fromArray($data),
        };
    }
}
