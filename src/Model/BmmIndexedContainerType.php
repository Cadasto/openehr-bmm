<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Indexed container type such as `Hash<String, EVENT_ACTION>`; `indexType` is the key type.
 */
readonly class BmmIndexedContainerType extends BmmContainerType
{
    public function __construct(
        string $containerType,
        public string $indexType,
        ?string $type = null,
        BmmContainerType|BmmGenericType|BmmSimpleType|null $typeDef = null,
    ) {
        parent::__construct($containerType, $type, $typeDef);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            '_type' => 'P_BMM_INDEXED_CONTAINER_TYPE',
            'container_type' => $this->containerType,
            'index_type' => $this->indexType,
            'type' => $this->type,
            'type_def' => $this->typeDef?->toArray(),
        ]);
    }

    /**
     * Create a BmmIndexedContainerType from an array representation
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            containerType: $data['container_type'],
            indexType: $data['index_type'],
            type: $data['type'] ?? null,
            typeDef: isset($data['type_def']) ? AbstractBmmType::fromArray($data['type_def']) : null,
        );
    }
}
