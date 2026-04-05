<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Codec;

use JsonException;
use RuntimeException;

final class JsonCodec implements BmmCodecInterface
{
    /**
     * @param string $content JSON string
     * @return array<string, mixed>
     * @throws JsonException
     */
    public function decode(string $content): array
    {
        /** @var array<string, mixed> */
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, mixed> $data
     * @throws JsonException
     */
    public function encode(array $data): string
    {
        return json_encode(
            $data,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
    }

    /**
     * Convenience: decode a JSON file from disk.
     *
     * @return array<string, mixed>
     * @throws RuntimeException If the file cannot be read
     * @throws JsonException
     */
    public function decodeFile(string $path): array
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("Cannot read file: {$path}");
        }
        return $this->decode($content);
    }
}
