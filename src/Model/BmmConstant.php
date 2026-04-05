<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM constant
 */
readonly class BmmConstant extends AbstractBmmModel
{
    /**
     * @param string $name
     * @param string $type
     * @param string|null $documentation
     * @param mixed|null $value
     */
    public function __construct(
        public string $name,
        public string $type,
        public ?string $documentation = null,
        public mixed $value = null,
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
        return array_filter([
            'name' => $this->name,
            'documentation' => $this->documentation,
            'type' => $this->type,
            'value' => $this->value,
        ]);
    }

    /**
     * Create a BmmConstant from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            documentation: $data['documentation'] ?? null,
            value: $data['value'] ?? null,
        );
    }
}
