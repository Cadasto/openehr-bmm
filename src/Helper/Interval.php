<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Helper;

readonly class Interval
{
    public function __construct(
        public ?int $lower = 0,
        public ?int $upper = null,
        public ?bool $lowerUnbounded = false,
        public ?bool $upperUnbounded = true,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'lower' => $this->lower,
            'upper' => $this->upper,
            'lower_unbounded' => $this->lowerUnbounded,
            'upper_unbounded' => $this->upperUnbounded,
        ], function (mixed $value): bool {
            // to force export default |0..*|
            return !is_null($value) && $value !== false;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            lower: $data['lower'] ?? 0,
            upper: $data['upper'] ?? null,
            lowerUnbounded: $data['lower_unbounded'] ?? false,
            // an upper value makes the interval bounded unless the flag says otherwise
            upperUnbounded: $data['upper_unbounded'] ?? !isset($data['upper']),
        );
    }
}
