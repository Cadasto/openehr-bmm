<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

readonly class BmmContainerType extends AbstractBmmType
{
    /**
     * @param string $containerType
     * @param string|null $type
     * @param BmmContainerType|BmmGenericType|BmmSimpleType|null $typeDef
     */
    public function __construct(
        public string $containerType,
        public ?string $type = null,
        public BmmContainerType|BmmGenericType|BmmSimpleType|null $typeDef = null,
    ) {
    }

    public function getName(): string
    {
        return $this->containerType;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            '_type' => 'P_BMM_CONTAINER_TYPE',
            'container_type' => $this->containerType,
            'type' => $this->type,
            'type_def' => $this->typeDef?->toArray(),
        ]);
    }

    /**
     * Create a BmmContainerType from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            containerType: $data['container_type'],
            type: $data['type'] ?? null,
            typeDef: isset($data['type_def']) ? AbstractBmmType::fromArray($data['type_def']) : null,
        );
    }
}
