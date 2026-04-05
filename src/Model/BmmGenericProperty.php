<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM generic property
 */
readonly class BmmGenericProperty extends AbstractBmmProperty
{
    /**
     * @param string $name
     * @param BmmGenericType $typeDef
     * @param string|null $documentation
     * @param bool $isMandatory
     */
    public function __construct(
        public string $name,
        public BmmGenericType $typeDef,
        public ?string $documentation = null,
        public bool $isMandatory = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $typeDef = $this->typeDef->toArray();
        unset($typeDef['_type']);
        return array_filter([
            '_type' => 'P_BMM_GENERIC_PROPERTY',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'is_mandatory' => $this->isMandatory,
            'type_def' => $typeDef,
        ]);
    }

    /**
     * Create a BmmGenericProperty from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            typeDef: BmmGenericType::fromArray($data['type_def']),
            documentation: $data['documentation'] ?? null,
            isMandatory: $data['is_mandatory'] ?? false,
        );
    }
}
