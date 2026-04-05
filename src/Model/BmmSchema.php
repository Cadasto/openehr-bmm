<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;
use InvalidArgumentException;

/**
 * Class representing the top-level BMM schema structure
 */
readonly class BmmSchema extends AbstractBmmModel
{
    public const string BMM_VERSION = '2.4';

    /**
     * @param string $rmPublisher
     * @param string $rmRelease
     * @param string $schemaName
     * @param string $schemaRevision
     * @param string $schemaLifecycleState
     * @param string $schemaDescription
     * @param string $schemaAuthor
     * @param Collection $packages
     * @param Collection $primitiveTypes
     * @param Collection $classDefinitions
     * @param Collection $includes
     * @param string|null $bmmVersion
     */
    public function __construct(
        public string $rmPublisher,
        public string $schemaName,
        public string $rmRelease,
        public string $schemaRevision,
        public string $schemaLifecycleState,
        public string $schemaDescription,
        public string $schemaAuthor,
        public Collection $packages,
        public Collection $primitiveTypes = new Collection(),
        public Collection $classDefinitions = new Collection(),
        public Collection $includes = new Collection(),
        public ?string $bmmVersion = self::BMM_VERSION,
    ) {
    }

    public function getSchemaId(): string
    {
        return $this->rmPublisher . '_' . $this->schemaName . '_' . $this->rmRelease;
    }

    public function getName(): string
    {
        return $this->getSchemaId();
    }

    public function getAlias(): ?string
    {
        return $this->schemaName;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'bmm_version' => $this->bmmVersion,
            'rm_publisher' => $this->rmPublisher,
            'schema_name' => $this->schemaName,
            'rm_release' => $this->rmRelease,
            'schema_revision' => $this->schemaRevision,
            'schema_lifecycle_state' => $this->schemaLifecycleState,
            'schema_description' => $this->schemaDescription ?: $this->schemaName,
            'schema_author' => $this->schemaAuthor,
            'includes' => $this->includes->toArray(),
            'packages' => $this->packages->toArray(),
            'primitive_types' => $this->primitiveTypes->toArray(),
            'class_definitions' => $this->classDefinitions->toArray(),
        ]);
    }

    /**
     * Create a BmmSchema from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $instance = new self(
            rmPublisher: $data['rm_publisher'],
            schemaName: $data['schema_name'],
            rmRelease: $data['rm_release'],
            schemaRevision: $data['schema_revision'],
            schemaLifecycleState: $data['schema_lifecycle_state'],
            schemaDescription: $data['schema_description'],
            schemaAuthor: $data['schema_author'],
            packages: new Collection(),
            primitiveTypes: new Collection(),
            classDefinitions: new Collection(),
            includes: new Collection(),
            bmmVersion: $data['bmm_version'] ?? self::BMM_VERSION,
        );

        if (!empty($data['packages']) && is_iterable($data['packages'])) {
            array_walk($data['packages'], function ($packageData) use ($instance) {
                $instance->packages->add(BmmPackage::fromArray($packageData));
            });
        } else {
            throw new InvalidArgumentException('Schema must contain at least one package');
        }

        if (!empty($data['primitive_types']) && is_iterable($data['primitive_types'])) {
            array_walk($data['primitive_types'], function ($primitiveTypeData) use ($instance) {
                $instance->primitiveTypes->add(AbstractBmmClass::fromArray($primitiveTypeData));
            });
        }
        if (!empty($data['class_definitions']) && is_iterable($data['class_definitions'])) {
            array_walk($data['class_definitions'], function ($classDefinitionData) use ($instance) {
                $instance->classDefinitions->add(AbstractBmmClass::fromArray($classDefinitionData));
            });
        }
        if (!empty($data['includes']) && is_iterable($data['includes'])) {
            array_walk($data['includes'], function ($includeData) use ($instance) {
                $instance->includes->add(BmmSchemaInclude::fromArray($includeData));
            });
        }

        return $instance;
    }


    public function getClassPackageQName(string $className): ?string
    {
        /** @var BmmPackage $package */
        foreach ($this->packages as $package) {
            $qname = $package->getClassPackageQName($className);
            if (!empty($qname)) {
                return $this->getName() . '.' . $qname;
            }
        }
        return null;
    }

    /**
     * BMM package path for a class (e.g. "org.openehr.rm.common"), or null if not in a package.
     */
    public function getClassPackagePath(string $className): ?string
    {
        $qname = $this->getClassPackageQName($className);
        if ($qname === null) {
            return null;
        }
        $pos = strpos($qname, 'org.openehr.');
        if ($pos === false) {
            return null;
        }
        return substr($qname, $pos);
    }

    /**
     * First package segment under the schema (e.g. "common", "data_structures") for two-level namespace.
     * Returns null if the class is not in a package or the path cannot be determined.
     */
    public function getClassSubpackageSegment(string $className): ?string
    {
        $path = $this->getClassPackagePath($className);
        if ($path === null) {
            return null;
        }
        $prefix = 'org.openehr.' . strtolower($this->schemaName) . '.';
        if (!str_starts_with($path, $prefix)) {
            return null;
        }
        $rest = substr($path, strlen($prefix));
        $first = explode('.', $rest)[0] ?? '';
        return $first !== '' ? $first : null;
    }
}
