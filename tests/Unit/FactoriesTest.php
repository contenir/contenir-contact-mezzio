<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Unit;

use Contenir\Contact\Business;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Exception\InvalidServiceException;
use Contenir\Contact\Mezzio\Factory\BusinessFactory;
use Contenir\Contact\Mezzio\Factory\ContactFactory;
use Contenir\Contact\Mezzio\Factory\ContactHelperFactory;
use Contenir\Contact\Mezzio\Factory\FormatsFactory;
use Contenir\Contact\Mezzio\Factory\HtmlRendererFactory;
use Contenir\Contact\Mezzio\Tests\TestAsset\ArrayContainer;
use Contenir\Contact\Mezzio\Tests\TestAsset\RecordingTemplates;
use Contenir\Contact\Mezzio\Tests\TestAsset\SiteConfig;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

use function count;

#[Group('unit')]
final class FactoriesTest extends TestCase
{
    /**
     * @param array<string, mixed> $services
     */
    private static function contact(array $services): Contact
    {
        $config = SiteConfig::make([
            'contenir_contact' => ['format' => ['footer' => ['part' => ['name'], 'template' => 'app::footer']]],
        ]);

        return (new ContactFactory())(new ArrayContainer([
            Business::class     => (new BusinessFactory())(new ArrayContainer(['config' => $config])),
            Formats::class      => (new FormatsFactory())(new ArrayContainer(['config' => $config])),
            HtmlRenderer::class => (new HtmlRendererFactory())(new ArrayContainer(['config' => $config])),
            ...$services,
        ]));
    }

    #[Test]
    public function aServiceOfTheWrongTypeIsRefused(): void
    {
        $this->expectException(InvalidServiceException::class);
        $this->expectExceptionMessage(
            'Service "Mezzio\Template\TemplateRendererInterface" must be a Mezzio\Template\TemplateRendererInterface, stdClass given',
        );

        self::contact([TemplateRendererInterface::class => new stdClass()]);
    }

    #[Test]
    public function linksMapsWithTheChosenProviderAndText(): void
    {
        $container = new ArrayContainer(['config' => SiteConfig::make([
            'map'              => ['provider' => 'google'],
            'contenir_contact' => ['map_text' => ' Directions '],
        ])]);
        $office = (new BusinessFactory())($container)->offices->primary();
        static::assertNotNull($office);

        static::assertStringContainsString(
            '<a class="contact__map" href="https://www.google.com/maps/search/?api=1&amp;query=-37.7787,144.9874">Directions</a>',
            (new HtmlRendererFactory())($container)->render($office, (new FormatsFactory())($container)->chosen([
                'format' => 'custom',
                'part'   => ['map'],
            ])),
        );
    }

    #[Test]
    public function readsTheBusinessFromTheSitesSettingsWithTheEnvironmentsOverThem(): void
    {
        $business = (new BusinessFactory())(new ArrayContainer([
            'config' => SiteConfig::make(['settings' => ['contact' => ['business' => ['name' => 'Esteta Local']]]]),
        ]));

        static::assertSame(['Esteta Local', 'k3f9q2'], [$business->name, $business->offices->primary()?->id]);
    }

    #[Test]
    public function readsTheSitesFormatsFromItsConfig(): void
    {
        $formats = (new FormatsFactory())(new ArrayContainer([
            'config' => ['contenir_contact' => ['format' => ['footer' => ['part' => ['address']]]]],
        ]));

        static::assertSame(['footer', null], [
            $formats->get('footer')?->name,
            (new FormatsFactory())(new ArrayContainer(['config' => 'none']))->get('footer'),
        ]);
    }

    #[Test]
    public function theContactServiceDrawsTemplatesWithTheApplicationsRenderer(): void
    {
        $templates = new RecordingTemplates();
        $contact   = self::contact([TemplateRendererInterface::class => $templates]);
        $office    = $contact->offices()->primary();
        static::assertNotNull($office);

        static::assertSame('template:app::footer', $contact->render($office, 'footer'));
    }

    #[Test]
    public function theHelperGivesTheContactService(): void
    {
        $contact = self::contact([]);

        static::assertSame($contact, (new ContactHelperFactory())(new ArrayContainer([Contact::class => $contact]))());
    }

    #[Test]
    public function theMapLinksTextIsMapUnlessTheConfigSaysOtherwise(): void
    {
        $container = new ArrayContainer(['config' => SiteConfig::make(['contenir_contact' => ['map_text' => ' ']])]);
        $office    = (new BusinessFactory())($container)->offices->primary();
        static::assertNotNull($office);

        static::assertStringContainsString(
            '>Map</a>',
            (new HtmlRendererFactory())($container)->render($office, (new FormatsFactory())($container)->chosen([
                'format' => 'custom',
                'part'   => ['map'],
            ])),
        );
    }

    #[Test]
    public function withoutARendererTheContactServiceDrawsPlainMarkup(): void
    {
        $contact = self::contact([]);
        $office  = $contact->offices()->primary();
        static::assertNotNull($office);

        static::assertStringStartsWith('<address class="contact contact--footer">', $contact->render(
            $office,
            'footer',
        ));
    }

    #[Test]
    public function withoutConfigThereIsABusinessWithoutOffices(): void
    {
        $business = (new BusinessFactory())(new ArrayContainer([]));

        static::assertSame(['', 0], [$business->name, count($business->offices)]);
    }
}
