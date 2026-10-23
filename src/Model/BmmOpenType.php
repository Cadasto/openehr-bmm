<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Reference to an open generic parameter such as `T` (P_BMM_OPEN_TYPE).
 *
 * In P_BMM this is a sibling of P_BMM_SIMPLE_TYPE. It extends BmmSimpleType here so that code written before
 * open types were modelled, which treats `T` as a simple type, keeps working; test for BmmOpenType first to
 * tell them apart.
 */
readonly class BmmOpenType extends BmmSimpleType
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            '_type' => 'P_BMM_OPEN_TYPE',
            'type' => $this->type,
        ];
    }

    /**
     * Create a BmmOpenType from an array representation
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
        );
    }
}
