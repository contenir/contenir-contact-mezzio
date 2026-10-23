<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function is_array;

/**
 * The parts of the application's config the factories read.
 *
 * @internal
 */
final class Config
{
    public const string KEY = 'contenir_contact';

    /**
     * The application's config, or an empty array.
     *
     * @return array<array-key, mixed>
     *
     * @throws ContainerExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment The application's config is untyped; it is checked here.
     */
    public static function application(ContainerInterface $container): array
    {
        $config = $container->has('config') ? $container->get('config') : null;

        return is_array($config) ? $config : [];
    }

    /**
     * The package's options, under contenir_contact.
     *
     * @return array<array-key, mixed>
     *
     * @throws ContainerExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment The application's config is untyped; it is checked here.
     */
    public static function options(ContainerInterface $container): array
    {
        $options = self::application($container)[self::KEY] ?? null;

        return is_array($options) ? $options : [];
    }
}
