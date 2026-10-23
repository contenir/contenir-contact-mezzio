<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Mezzio\Exception\InvalidServiceException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Gets a service by its type's name, checking it is one.
 *
 * @internal
 */
final class Services
{
    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     *
     * @throws ContainerExceptionInterface
     * @throws InvalidServiceException When the service has another type.
     *
     * @mago-expect analysis:mixed-assignment Container services are untyped; the type is checked here.
     */
    public static function get(ContainerInterface $container, string $type): object
    {
        $service = $container->get($type);
        if (! $service instanceof $type) {
            throw InvalidServiceException::forService($type, $service);
        }

        return $service;
    }
}
