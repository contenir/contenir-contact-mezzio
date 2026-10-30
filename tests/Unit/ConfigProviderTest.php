<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Unit;

use Contenir\Contact\Business;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\ConfigProvider;
use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Factory\BusinessFactory;
use Contenir\Contact\Mezzio\Factory\ContactFactory;
use Contenir\Contact\Mezzio\Factory\ContactHelperFactory;
use Contenir\Contact\Mezzio\Factory\EscapeEmailHelperFactory;
use Contenir\Contact\Mezzio\Factory\FormatsFactory;
use Contenir\Contact\Mezzio\Factory\HtmlRendererFactory;
use Contenir\Contact\Mezzio\View\ContactHelper;
use Contenir\Contact\Mezzio\View\EscapeEmailHelper;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ConfigProviderTest extends TestCase
{
    #[Test]
    public function givesItsDependenciesAndViewHelpersApart(): void
    {
        $provider = new ConfigProvider();

        static::assertSame(
            [$provider()['dependencies'], $provider()['view_helpers']],
            [$provider->getDependencies(), $provider->getViewHelpers()],
        );
    }

    #[Test]
    public function registersTheServicesAndTheViewHelpers(): void
    {
        static::assertSame(
            [
                'dependencies' => [
                    'factories' => [
                        Business::class     => BusinessFactory::class,
                        Contact::class      => ContactFactory::class,
                        Formats::class      => FormatsFactory::class,
                        HtmlRenderer::class => HtmlRendererFactory::class,
                    ],
                ],
                'view_helpers' => [
                    'aliases'   => [
                        'contact'     => ContactHelper::class,
                        'escapeEmail' => EscapeEmailHelper::class,
                    ],
                    'factories' => [
                        ContactHelper::class     => ContactHelperFactory::class,
                        EscapeEmailHelper::class => EscapeEmailHelperFactory::class,
                    ],
                ],
            ],
            (new ConfigProvider())(),
        );
    }
}
