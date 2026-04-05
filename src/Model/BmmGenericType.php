<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

readonly class BmmGenericType extends AbstractBmmType
{
    /**
     * @param string $rootType
     * @param Collection $genericParameterDefs
     * @param array<string> $genericParameters
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
        $genericParameterDefs = [];
        /** @var AbstractBmmType $def */
        foreach ($this->genericParameterDefs as $key => $def) {
            $genericParameterDefs[$key] = $def->toArray();
        }

        return array_filter([
            '_type' => 'P_BMM_GENERIC_TYPE',
            'root_type' => $this->rootType,
            'generic_parameter_defs' => $genericParameterDefs,
            'generic_parameters' => $this->genericParameters,
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
        if (!empty($data['generic_parameter_defs']) && is_iterable($data['generic_parameter_defs'])) {
            array_walk($data['generic_parameter_defs'], function ($genericParameterDefData, $key) use ($instance) {
                $instance->genericParameterDefs->set(
                    (string) $key,
                    AbstractBmmType::fromArray($genericParameterDefData),
                );
            });
        }
        return $instance;
    }
}
