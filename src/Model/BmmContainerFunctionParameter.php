<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Interval;

/**
 * Class representing a BMM container function parameter
 */
readonly class BmmContainerFunctionParameter extends AbstractBmmFunctionParameter
{
    /**
     * @param string $name
     * @param BmmContainerType $typeDef
     * @param string|null $documentation
     * @param bool $isNullable
     * @param Interval|null $cardinality
     */
    public function __construct(
        public string $name,
        public BmmContainerType $typeDef,
        public ?string $documentation = null,
        public bool $isNullable = false,
        public ?Interval $cardinality = new Interval(),
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
            '_type' => 'P_BMM_CONTAINER_FUNCTION_PARAMETER',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'is_nullable' => $this->isNullable,
            'type_def' => $typeDef,
            'cardinality' => $this->cardinality?->toArray(),
        ]);
    }

    /**
     * Create a BmmContainerFunctionParameter from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            typeDef: BmmContainerType::fromArray($data['type_def']),
            documentation: $data['documentation'] ?? null,
            isNullable: $data['is_nullable'] ?? false,
            cardinality: isset($data['cardinality']) ? Interval::fromArray($data['cardinality']) : new Interval(),
        );
    }
}
