<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

readonly class BmmSimpleType extends AbstractBmmType
{
    /**
     * @param string $type
     */
    public function __construct(
        public string $type,
    ) {
    }

    public function getName(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            '_type' => 'P_BMM_SIMPLE_TYPE',
            'type' => $this->type,
        ];
    }

    /**
     * Create a BmmSimpleType from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
        );
    }
}
