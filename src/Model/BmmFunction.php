<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

/**
 * Class representing a BMM function
 */
readonly class BmmFunction extends AbstractBmmModel
{
    /**
     * @param string $name
     * @param array<string, string> $aliases
     * @param string|null $documentation
     * @param bool $isAbstract
     * @param Collection $parameters
     * @param array<string, string> $preConditions
     * @param array<string, string> $postConditions
     * @param BmmContainerType|BmmGenericType|BmmSimpleType|null $result
     * @param bool $isNullable
     */
    public function __construct(
        public string $name,
        public array $aliases = [],
        public ?string $documentation = null,
        public bool $isAbstract = false,
        public Collection $parameters = new Collection(),
        public array $preConditions = [],
        public array $postConditions = [],
        public BmmContainerType|BmmGenericType|BmmSimpleType|null $result = null,
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
            'name' => $this->name,
            'aliases' => $this->aliases,
            'documentation' => $this->documentation,
            'is_abstract' => $this->isAbstract,
            'parameters' => $this->parameters->toArray(),
            'pre_conditions' => $this->preConditions,
            'post_conditions' => $this->postConditions,
            'result' => $this->result?->toArray(),
            'is_nullable' => $this->isNullable,
        ]);
    }

    /**
     * Create a BmmFunction from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $instance = new self(
            name: $data['name'],
            aliases: $data['aliases'] ?? [],
            documentation: $data['documentation'] ?? null,
            isAbstract: $data['is_abstract'] ?? false,
            parameters: new Collection(),
            preConditions: $data['pre_conditions'] ?? [],
            postConditions: $data['post_conditions'] ?? [],
            result: isset($data['result']) ? AbstractBmmType::fromArray($data['result']) : null,
            isNullable: $data['is_nullable'] ?? false,
        );

        if (!empty($data['parameters']) && is_iterable($data['parameters'])) {
            array_walk($data['parameters'], function ($parameterData, $parameterName) use ($instance) {
                $param = AbstractBmmFunctionParameter::fromArray($parameterData);
                $instance->parameters->offsetSet($parameterName, $param);
            });
        }

        return $instance;
    }
}
