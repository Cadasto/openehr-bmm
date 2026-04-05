<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

/**
 * Class representing a BMM Interface definition
 */
readonly class BmmInterface extends AbstractBmmClass
{
    /**
     * @param string $name
     * @param string|null $documentation
     * @param Collection $functions
     */
    public function __construct(
        public string $name,
        public ?string $documentation = null,
        public Collection $functions = new Collection(),
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
            '_type' => 'P_BMM_INTERFACE',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'functions' => $this->functions->toArray(),
        ]);
    }

    /**
     * Create a BmmInterface from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $instance = new self(
            name: $data['name'],
            documentation: $data['documentation'] ?? null,
            functions: new Collection(),
        );

        if (!empty($data['functions']) && is_iterable($data['functions'])) {
            array_walk($data['functions'], function ($functionData) use ($instance) {
                $instance->functions->add(BmmFunction::fromArray($functionData));
            });
        }

        return $instance;
    }
}
