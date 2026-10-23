<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Business;
use Contenir\Contact\ContactSettings;
use Contenir\Contact\Mezzio\SiteSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * The business and its offices from the site's settings.
 *
 * @api
 */
final class BusinessFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Business
    {
        return ContactSettings::fromSettings(SiteSettings::fromConfig(Config::application($container)));
    }
}
