<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

/**
 * Class representing a BMM String-based Enumeration
 */
readonly class BmmEnumerationString extends AbstractBmmClass
{
    /**
     * @param string $name
     * @param string|null $documentation
     * @param array<string> $ancestors
     * @param array<string> $itemNames
     * @param array<string> $itemValues
     * @param array<string> $itemDocumentations
     * @param Collection $functions
     */
    public function __construct(
        public string $name,
        public ?string $documentation = null,
        public array $ancestors = ['String'],
        public array $itemNames = [],
        public array $itemValues = [],
        public array $itemDocumentations = [],
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
            '_type' => 'P_BMM_ENUMERATION_STRING',
            'name' => $this->name,
            'documentation' => $this->documentation,
            'ancestors' => $this->ancestors,
            'item_names' => $this->itemNames,
            'item_values' => $this->itemValues,
            'item_documentations' => $this->itemDocumentations,
            'functions' => $this->functions->toArray(),
        ]);
    }

    /**
     * Create a BmmEnumerationString from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $instance = new self(
            name: $data['name'],
            documentation: $data['documentation'] ?? null,
            ancestors: $data['ancestors'] ?? ['String'],
            itemNames: $data['item_names'] ?? [],
            itemValues: $data['item_values'] ?? [],
            itemDocumentations: $data['item_documentations'] ?? [],
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
