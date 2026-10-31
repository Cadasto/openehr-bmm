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
     * @param array<string> $ancestors
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
        public Collection $ancestorDefs = new Collection(),
        /**
         * @deprecated To be removed in 0.5.0. P_BMM does not persist `is_override`: tools infer it when they merge
         *     included schemas, which this library does not do. It is read only when a file carries it, so `false`
         *     means the file did not say, not that the class overrides nothing.
         */
        public bool $isOverride = false,
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
            'is_override' => $this->isOverride,
            'ancestors' => $this->ancestors,
            'ancestor_defs' => $this->ancestorDefs->toArray(),
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
            ancestorDefs: new Collection(),
            isOverride: $data['is_override'] ?? false,
        );

        // A map keyed by type signature, or a list as in the class model before SPECLANG-16; keep either shape.
        // ancestor_defs replaces ancestors, so a non-generic ancestor can appear here too; an entry without
        // `_type` is a generic type, the only kind the old list form allowed.
        $instance->ancestorDefs->populateFrom(
            $data['ancestor_defs'] ?? [],
            static fn (array $item): AbstractBmmType => isset($item['_type'])
                ? AbstractBmmType::fromArray($item)
                : BmmGenericType::fromArray($item),
            keyed: true,
        );
        $instance->genericParameterDefs->populateFrom(
            $data['generic_parameter_defs'] ?? [],
            BmmGenericParameter::fromArray(...),
        );
        $instance->constants->populateFrom(
            $data['constants'] ?? [],
            BmmConstant::fromArray(...),
        );
        $instance->properties->populateFrom(
            $data['properties'] ?? [],
            AbstractBmmProperty::fromArray(...),
        );
        $instance->functions->populateFrom(
            $data['functions'] ?? [],
            BmmFunction::fromArray(...),
        );

        return $instance;
    }
}
