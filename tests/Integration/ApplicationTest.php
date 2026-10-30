<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Integration;

use Contenir\Contact\Mezzio\ConfigProvider;
use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Tests\TestAsset\RecordingTemplates;
use Contenir\Contact\Mezzio\Tests\TestAsset\SiteConfig;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The package in a real service container, with the contact() helper drawn
 * through laminas-view.
 */
#[Group('integration')]
final class ApplicationTest extends TestCase
{
    /**
     * @param array<string, object> $services
     */
    private static function container(array $services = []): ServiceManager
    {
        $config = (new ConfigProvider())();

        return new ServiceManager([
            ...$config['dependencies'],
            'services' => [
                'config' => SiteConfig::make([
                    'settings'         => ['site' => ['title' => 'Studio Esteta (local)']],
                    'contenir_contact' => [
                        'format' => ['footer' => ['part' => ['name', 'address'], 'template' => 'app::footer']],
                    ],
                ]),
                ...$services,
            ],
        ]);
    }

    private static function view(ServiceManager $container): PhpRenderer
    {
        $renderer = new PhpRenderer();
        $renderer->setHelperPluginManager(
            new HelperPluginManager($container, (new ConfigProvider())->getViewHelpers()),
        );

        return $renderer;
    }

    #[Test]
    public function aFormatsTemplateIsDrawnByTheApplicationsRenderer(): void
    {
        $templates = new RecordingTemplates();
        $container = self::container([TemplateRendererInterface::class => $templates]);
        $contact   = $container->get(Contact::class);

        static::assertSame(
            ['template:app::footer', 'Studio Esteta (local)'],
            [$contact->render($contact->business(), 'footer'), $contact->business()->name],
        );
    }

    #[Test]
    public function theEscapeEmailHelperWritesALinkThroughLaminasView(): void
    {
        $escape = self::view(self::container())->plugin('escapeEmail')();

        static::assertSame(
            '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;%61%40%62%2e%61%75">&#97;&#64;&#98;&#46;&#97;&#117;</a>',
            "<a href=\"{$escape->href('a@b.au')}\">{$escape->text('a@b.au')}</a>",
        );
    }

    #[Test]
    public function theHelperDrawsAnOfficeThroughLaminasView(): void
    {
        $view    = self::view(self::container());
        $contact = $view->plugin('contact')();
        static::assertInstanceOf(Contact::class, $contact);

        $office = $contact->offices()->primary();
        static::assertNotNull($office);

        static::assertSame(
            "<address class=\"contact contact--address\">\n<strong class=\"contact__name\">Melbourne studio</strong>\n"
                . "<span class=\"contact__address\">402 Queens Parade<br>Fitzroy North VIC 3068</span>\n</address>",
            $contact->render($office, 'address'),
        );
    }
}
