<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\Collection;

/**
 * Class representing a BMM package
 */
readonly class BmmPackage extends AbstractBmmModel
{
    /**
     * @param string $name
     * @param Collection $packages
     * @param array<string> $classes
     */
    public function __construct(
        public string $name,
        public Collection $packages = new Collection(),
        public array $classes = []
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
            'packages' => $this->packages->toArray(),
            'classes' => $this->classes,
        ]);
    }

    /**
     * Create a BmmPackage from an array representation
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $instance = new self(
            name: $data['name'],
            packages: new Collection(),
            classes: $data['classes'] ?? [],
        );
        if (!empty($data['packages']) && is_iterable($data['packages'])) {
            array_walk($data['packages'], function ($packageData) use ($instance) {
                $instance->packages->add(BmmPackage::fromArray($packageData));
            });
        }

        return $instance;
    }

    /**
     * @return array<string>
     */
    public function getAllClassNames(): array
    {
        $classes = $this->classes;
        /** @var BmmPackage $package */
        foreach ($this->packages as $package) {
            $classes = array_merge($classes, $package->getAllClassNames());
        }

        return $classes;
    }


    public function getClassPackageQName(string $className): ?string
    {
        if (in_array($className, $this->classes)) {
            return $this->getName();
        }
        /** @var BmmPackage $package */
        foreach ($this->packages as $package) {
            $qname = $package->getClassPackageQName($className);
            if (!empty($qname)) {
                return $this->getName() . '.' . $qname;
            }
        }
        return null;
    }
}
