<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

/**
 * Class representing a BMM class definition
 */
readonly class BmmClass extends AbstractBmmClass
{
    /**
     * @param string $name
     * @param bool $isAbstract
     * @param array<string> $ancestors
     * @param string|null $documentation
     * @param Collection $genericParameterDefs
     * @param Collection $constants
     * @param Collection $properties
     * @param Collection $functions
     * @param array<string,string> $invariants
     */
    public function __construct(
        public string $name,
        public bool $isAbstract = false,
        public array $ancestors = [],
        public ?string $documentation = null,
        public Collection $genericParameterDefs = new Collection(),
        public Collection $constants = new Collection(),
        public Collection $properties = new Collection(),
        public Collection $functions = new Collection(),
        public array $invariants = [],
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
            'is_abstract' => $this->isAbstract,
            'ancestors' => $this->ancestors,
            'generic_parameter_defs' => $this->genericParameterDefs->toArray(),
            'constants' => $this->constants->toArray(),
            'properties' => $this->properties->toArray(),
            'functions' => $this->functions->toArray(),
            'invariants' => $this->invariants,
        ]);
    }

    /**
     * Create a BmmClass from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        if (!empty($data['invariants']) && is_iterable($data['invariants'])) {
            array_walk($data['invariants'], function (&$invariantData) {
                $invariantData = (string)$invariantData;
            });
        }
        $instance = new self(
            name: $data['name'],
            isAbstract: $data['is_abstract'] ?? false,
            ancestors: $data['ancestors'] ?? [],
            documentation: $data['documentation'] ?? null,
            genericParameterDefs: new Collection(),
            constants: new Collection(),
            properties: new Collection(),
            functions: new Collection(),
            invariants: $data['invariants'] ?? [],
        );

        if (!empty($data['generic_parameter_defs']) && is_iterable($data['generic_parameter_defs'])) {
            array_walk($data['generic_parameter_defs'], function ($genericParameterDefData) use ($instance) {
                $instance->genericParameterDefs->add(BmmGenericParameter::fromArray($genericParameterDefData));
            });
        }
        if (!empty($data['constants']) && is_iterable($data['constants'])) {
            array_walk($data['constants'], function ($constantData) use ($instance) {
                $instance->constants->add(BmmConstant::fromArray($constantData));
            });
        }
        if (!empty($data['properties']) && is_iterable($data['properties'])) {
            array_walk($data['properties'], function ($propertyData) use ($instance) {
                $instance->properties->add(AbstractBmmProperty::fromArray($propertyData));
            });
        }
        if (!empty($data['functions']) && is_iterable($data['functions'])) {
            array_walk($data['functions'], function ($functionData) use ($instance) {
                $instance->functions->add(BmmFunction::fromArray($functionData));
            });
        }

        return $instance;
    }
}
