<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function get_debug_type;
use function sprintf;

/**
 * A service the container gives isn't of the type the package needs.
 *
 * @api
 */
final class InvalidServiceException extends RuntimeException implements ContainerExceptionInterface
{
    public static function forService(string $type, mixed $service): self
    {
        return new self(sprintf('Service "%1$s" must be a %1$s, %2$s given', $type, get_debug_type($service)));
    }
}
