<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Helper;

/**
 * Cardinality of a container, read as a multiplicity: a lower bound from 0 and an upper
 * bound or `*`, both included. P_BMM writes it as `lower` plus `upper` or `upper_unbounded`.
 */
readonly class Interval
{
    public ?int $lower;
    public ?int $upper;
    public ?bool $lowerUnbounded;
    public ?bool $upperUnbounded;

    public function __construct(
        ?int $lower = 0,
        ?int $upper = null,
        ?bool $lowerUnbounded = false,
        ?bool $upperUnbounded = null,
    ) {
        // a count has no open lower side, so an open lower bound is 0
        $this->lower = $lowerUnbounded === true ? 0 : ($lower ?? 0);
        $this->lowerUnbounded = false;
        // no upper bound means open above, and the open flag wins over a stray bound
        $this->upperUnbounded = $upper === null || $upperUnbounded === true;
        $this->upper = $this->upperUnbounded ? null : $upper;
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
        $lower = $data['lower'] ?? null;
        $upper = $data['upper'] ?? null;
        // counts are whole numbers, so an excluded bound is the included one next to it
        if (is_int($lower) && ($data['lower_included'] ?? true) === false) {
            $lower++;
        }
        if (is_int($upper) && ($data['upper_included'] ?? true) === false) {
            $upper--;
        }

        return new self(
            lower: $lower,
            upper: $upper,
            lowerUnbounded: $data['lower_unbounded'] ?? false,
            upperUnbounded: $data['upper_unbounded'] ?? null,
        );
    }
}
