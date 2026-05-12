<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Helper;

use ArrayObject;
use JsonSerializable;

/**
 * @template-extends ArrayObject<string, CollectableInterface>
 */
class Collection extends ArrayObject implements JsonSerializable
{
    /** @var array<string, string> */
    public array $aliases = [];

    public function add(CollectableInterface $item, ?string $additionalAlias = null): void
    {
        $key = $item->getName() ?: get_class($item);
        $this->offsetSet($key, $item);
        if ($item->getAlias()) {
            $this->aliases[$item->getAlias()] = $key;
        }
        if ($additionalAlias) {
            $this->aliases[$additionalAlias] = $key;
        }
    }

    /**
     * Store an item under an explicit key.
     *
     * Use this when the map key comes from the P_BMM document (e.g. generic formal parameter
     * names such as T, K, V) and must not be derived from {@see CollectableInterface::getName()}.
     * For maps where the key is always the element’s name, prefer {@see add()}.
     */
    public function set(string $key, CollectableInterface $item): void
    {
        $this->offsetSet($key, $item);
    }

    public function get(string $key): ?CollectableInterface
    {
        $key = $this->aliases[$key] ?? $key;
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Populate this collection from raw data, applying $factory to each element.
     *
     * When $keyed is true the source map key is preserved via {@see set()};
     * otherwise the item's name is used as key via {@see add()}. Non-iterable
     * or empty input is a silent no-op, matching the historical
     * `if (!empty(...) && is_iterable(...))` guard at every call site.
     *
     * @param mixed $items   Raw data; typically array<string, mixed> from a P_BMM document.
     * @param callable(mixed): CollectableInterface $factory
     */
    public function populateFrom(mixed $items, callable $factory, bool $keyed = false): void
    {
        if (empty($items) || !is_iterable($items)) {
            return;
        }
        foreach ($items as $key => $data) {
            $item = $factory($data);
            if ($keyed) {
                $this->set((string) $key, $item);
            } else {
                $this->add($item);
            }
        }
    }

    public function flush(): void
    {
        $this->aliases = [];
        $this->exchangeArray([]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_map(
            static fn(CollectableInterface $item): array => $item->toArray(),
            $this->getArrayCopy(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
