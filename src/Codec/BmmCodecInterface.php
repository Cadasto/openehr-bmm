<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Codec;

interface BmmCodecInterface
{
    /**
     * Decode a string into an array representation.
     *
     * @param string $content Encoded content (JSON, YAML, etc.)
     * @return array<string, mixed>
     */
    public function decode(string $content): array;

    /**
     * Encode an array representation into a string.
     *
     * @param array<string, mixed> $data
     * @return string
     */
    public function encode(array $data): string;
}
