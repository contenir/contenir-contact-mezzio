<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Business;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Exception\InvalidServiceException;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * The Contact service, drawing formats with templates through the
 * application's TemplateRendererInterface when it has one.
 *
 * @api
 */
final class ContactFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidServiceException When a service isn't what it should be.
     */
    public function __invoke(ContainerInterface $container): Contact
    {
        return new Contact(
            Services::get($container, Business::class),
            Services::get($container, Formats::class),
            Services::get($container, HtmlRenderer::class),
            $container->has(TemplateRendererInterface::class)
                ? Services::get($container, TemplateRendererInterface::class)
                : null,
        );
    }
}
