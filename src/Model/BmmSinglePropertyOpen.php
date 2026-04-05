<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM single property typed with an open/generic parameter (e.g. `T`).
 */
readonly class BmmSinglePropertyOpen extends AbstractBmmProperty
{
    /**
     * @param string $name
     * @param string $type Open type name (generic parameter), e.g. `T`, `K`
     * @param string|null $documentation
     * @param bool $isMandatory
     * @param mixed $default
     */
    public function __construct(
        public string $name,
        public string $type,
        public ?string $documentation = null,
        public bool $isMandatory = false,
        public mixed $default = null,
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
        $result = array_filter([
            '_type' => 'P_BMM_SINGLE_PROPERTY_OPEN',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'is_mandatory' => $this->isMandatory,
            'type' => $this->type,
        ]);
        if ($this->default !== null) {
            $result['default'] = $this->default;
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'] ?? 'Any',
            documentation: $data['documentation'] ?? null,
            isMandatory: $data['is_mandatory'] ?? false,
            default: $data['default'] ?? null,
        );
    }
}
