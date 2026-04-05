<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM single function parameter
 */
readonly class BmmSingleFunctionParameterOpen extends AbstractBmmFunctionParameter
{
    /**
     * @param string $name
     * @param string $type
     * @param string|null $documentation
     * @param bool $isNullable
     */
    public function __construct(
        public string $name,
        public string $type,
        public ?string $documentation = null,
        public bool $isNullable = false,
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
            '_type' => 'P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'is_nullable' => $this->isNullable,
            'type' => $this->type,
        ]);
    }

    /**
     * Create a BmmSingleFunctionParameterOpen from an array representation
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
            isNullable: $data['is_nullable'] ?? false,
        );
    }
}
