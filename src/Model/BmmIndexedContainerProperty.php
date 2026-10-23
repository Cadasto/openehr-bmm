<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Interval;

/**
 * Class representing a BMM indexed container property, e.g. `Hash<String, EVENT_ACTION>`.
 * `typeDef` always holds a BmmIndexedContainerType; use getIndexedTypeDef() for typed access to it.
 */
readonly class BmmIndexedContainerProperty extends BmmContainerProperty
{
    // PHP does not let a subclass narrow the inherited BmmContainerType $typeDef, so keep a typed reference.
    private BmmIndexedContainerType $indexedTypeDef;

    public function __construct(
        string $name,
        BmmIndexedContainerType $typeDef,
        ?string $documentation = null,
        bool $isMandatory = false,
        ?Interval $cardinality = new Interval(),
    ) {
        parent::__construct($name, $typeDef, $documentation, $isMandatory, $cardinality);
        $this->indexedTypeDef = $typeDef;
    }

    public function getIndexedTypeDef(): BmmIndexedContainerType
    {
        return $this->indexedTypeDef;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['_type' => 'P_BMM_INDEXED_CONTAINER_PROPERTY'] + parent::toArray();
    }

    /**
     * Create a BmmIndexedContainerProperty from an array representation
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            typeDef: BmmIndexedContainerType::fromArray($data['type_def']),
            documentation: $data['documentation'] ?? null,
            isMandatory: $data['is_mandatory'] ?? false,
            cardinality: isset($data['cardinality']) ? Interval::fromArray($data['cardinality']) : new Interval(),
        );
    }
}
