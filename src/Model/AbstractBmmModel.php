<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

use Cadasto\OpenEHR\BMM\Helper\CollectableInterface;
use JsonSerializable;

/**
 * Base class for all BMM model elements that participate in collections and serialization.
 */
abstract readonly class AbstractBmmModel implements CollectableInterface, JsonSerializable
{
    public function getAlias(): ?string
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
