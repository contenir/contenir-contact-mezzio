<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Exception\InvalidServiceException;
use Contenir\Contact\Mezzio\View\ContactHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * @api
 */
final class ContactHelperFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidServiceException When the Contact service isn't one.
     */
    public function __invoke(ContainerInterface $container): ContactHelper
    {
        return new ContactHelper(Services::get($container, Contact::class));
    }
}
