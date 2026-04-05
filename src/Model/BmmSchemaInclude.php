<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Class representing a BMM schema include reference.
 */
readonly class BmmSchemaInclude extends AbstractBmmModel
{
    /**
     * @param string $id
     */
    public function __construct(
        public string $id,
    ) {
    }


    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
        ]);
    }

    /**
     * Create a BmmSchemaInclude from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }

    public function getName(): string
    {
        return $this->id;
    }
}
