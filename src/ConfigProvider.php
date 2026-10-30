<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio;

use Contenir\Contact\Business;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\Factory\BusinessFactory;
use Contenir\Contact\Mezzio\Factory\ContactFactory;
use Contenir\Contact\Mezzio\Factory\ContactHelperFactory;
use Contenir\Contact\Mezzio\Factory\EscapeEmailHelperFactory;
use Contenir\Contact\Mezzio\Factory\FormatsFactory;
use Contenir\Contact\Mezzio\Factory\HtmlRendererFactory;
use Contenir\Contact\Mezzio\View\ContactHelper;
use Contenir\Contact\Mezzio\View\EscapeEmailHelper;

/**
 * The package's services, and the contact() and escapeEmail() view helpers for laminas-view.
 *
 * @api
 */
final class ConfigProvider
{
    /**
     * @return array{factories: array<class-string, class-string>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                Business::class     => BusinessFactory::class,
                Contact::class      => ContactFactory::class,
                Formats::class      => FormatsFactory::class,
                HtmlRenderer::class => HtmlRendererFactory::class,
            ],
        ];
    }

    /**
     * @return array{aliases: array<string, class-string>, factories: array<class-string, class-string>}
     */
    public function getViewHelpers(): array
    {
        return [
            'aliases'   => [
                'contact'     => ContactHelper::class,
                'escapeEmail' => EscapeEmailHelper::class,
            ],
            'factories' => [
                ContactHelper::class     => ContactHelperFactory::class,
                EscapeEmailHelper::class => EscapeEmailHelperFactory::class,
            ],
        ];
    }

    /**
     * @return array<string, array<string, array<string, string>>>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'view_helpers' => $this->getViewHelpers(),
        ];
    }
}
