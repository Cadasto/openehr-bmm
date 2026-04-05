<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Helper;

interface CollectableInterface
{
    public function getName(): string;

    public function getAlias(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
