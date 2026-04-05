<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM generic parameter
 */
readonly class BmmGenericParameter extends AbstractBmmModel
{
    /**
     * @param string $name
     * @param string|null $conformsToType
     */
    public function __construct(
        public string $name,
        public ?string $conformsToType = null,
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
            'conforms_to_type' => $this->conformsToType,
        ]);
    }

    /**
     * Create a BmmGenericParameter from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            conformsToType: $data['conforms_to_type'] ?? null,
        );
    }
}
