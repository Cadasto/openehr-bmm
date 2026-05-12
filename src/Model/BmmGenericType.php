<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

readonly class BmmGenericType extends AbstractBmmType
{
    /**
     * @param string $rootType
     * @param Collection $genericParameterDefs
     * @param array<string|AbstractBmmType> $genericParameters
     */
    public function __construct(
        public string $rootType,
        public Collection $genericParameterDefs = new Collection(),
        public array $genericParameters = [],
    ) {
    }

    public function getName(): string
    {
        return $this->rootType;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $genericParameters = array_map(
            static fn(string|AbstractBmmType $p): mixed => $p instanceof AbstractBmmType ? $p->toArray() : $p,
            $this->genericParameters,
        );

        return array_filter([
            '_type' => 'P_BMM_GENERIC_TYPE',
            'root_type' => $this->rootType,
            'generic_parameter_defs' => $this->genericParameterDefs->toArray(),
            'generic_parameters' => $genericParameters,
        ]);
    }

    /**
     * Create a BmmGenericType from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $genericParameters = array_map(function ($genericParameter) {
            if (is_array($genericParameter)) {
                return AbstractBmmType::fromArray($genericParameter);
            }
            return $genericParameter;
        }, $data['generic_parameters'] ?? []);
        $instance = new self(
            rootType: $data['root_type'],
            genericParameterDefs: new Collection(),
            genericParameters: $genericParameters,
        );
        $instance->genericParameterDefs->populateFrom(
            $data['generic_parameter_defs'] ?? [],
            AbstractBmmType::fromArray(...),
            keyed: true,
        );
        return $instance;
    }
}
