<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\BMM\Model;

/**
 * Abstract BMM function parameter — polymorphic fromArray() dispatcher.
 */
abstract readonly class AbstractBmmFunctionParameter extends AbstractBmmModel
{
    /**
     * @param array<string, mixed> $data
     */
    // phpcs:ignore Generic.Files.LineLength.TooLong
    public static function fromArray(array $data): BmmContainerFunctionParameter|BmmGenericFunctionParameter|BmmSingleFunctionParameter|BmmSingleFunctionParameterOpen
    {
        $type = $data['_type'] ?? 'P_BMM_SINGLE_FUNCTION_PARAMETER';
        return match ($type) {
            'P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN' => BmmSingleFunctionParameterOpen::fromArray($data),
            'P_BMM_CONTAINER_FUNCTION_PARAMETER' => BmmContainerFunctionParameter::fromArray($data),
            'P_BMM_GENERIC_FUNCTION_PARAMETER' => BmmGenericFunctionParameter::fromArray($data),
            default => BmmSingleFunctionParameter::fromArray($data),
        };
    }
}
