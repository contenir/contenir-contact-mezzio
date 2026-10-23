<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Exception\InvalidFormatException;
use Contenir\Contact\Format\Formats;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * The package's formats with the site's from contenir_contact.format.
 *
 * @api
 */
final class FormatsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidFormatException When a format in the config is wrong.
     */
    public function __invoke(ContainerInterface $container): Formats
    {
        return Formats::fromConfig(Config::options($container)['format'] ?? null);
    }
}
