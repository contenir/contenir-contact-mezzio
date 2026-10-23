<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Unit;

use Contenir\Contact\Business;
use Contenir\Contact\ContactSettings;
use Contenir\Contact\Format\Format;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\Contact;
use Contenir\Contact\Mezzio\Exception\UnknownFormatException;
use Contenir\Contact\Mezzio\Tests\TestAsset\RecordingTemplates;
use Contenir\Contact\Mezzio\Tests\TestAsset\SiteConfig;
use Contenir\Contact\Office;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_key_exists;
use function is_array;
use function str_contains;

#[Group('unit')]
final class ContactTest extends TestCase
{
    private static function business(): Business
    {
        return ContactSettings::fromSettings(SiteConfig::make());
    }

    private static function contact(?RecordingTemplates $templates = null): Contact
    {
        return new Contact(
            self::business(),
            Formats::fromConfig([
                'footer' => ['part' => ['name', 'address'], 'template' => 'app::office-footer'],
                'header' => ['part' => ['telephone']],
            ]),
            new HtmlRenderer(),
            $templates,
        );
    }

    private static function office(): Office
    {
        $office = self::business()->offices->primary();
        static::assertNotNull($office);

        return $office;
    }

    #[Test]
    public function aFormatWithAnEmptyTemplateIsPlainMarkup(): void
    {
        $templates = new RecordingTemplates();
        $html      = self::contact($templates)->render(self::office(), new Format('x', [], template: ''));

        static::assertSame(['', ''], [$html, $templates->name]);
    }

    #[Test]
    public function aTemplateDrawingTheBusinessIsGivenNoOffice(): void
    {
        $templates = new RecordingTemplates();
        self::contact($templates)->render(self::business(), 'footer');

        static::assertSame(
            [true, null, false],
            [
                is_array($templates->params) && array_key_exists('office', $templates->params),
                is_array($templates->params) ? $templates->params['office'] ?? null : 'not an array',
                is_array($templates->params) ? $templates->params['layout'] ?? null : 'not an array',
            ],
        );
    }

    #[Test]
    public function drawsAFormatWithATemplateByTheTemplate(): void
    {
        $templates = new RecordingTemplates();
        $office    = self::office();
        $html      = self::contact($templates)->render($office, 'footer');

        static::assertEquals(
            [
                'template:app::office-footer',
                'app::office-footer',
                [
                    'office'   => $office,
                    'business' => self::business(),
                    'format'   => Format::fromConfig('footer', [
                        'part'     => ['name', 'address'],
                        'template' => 'app::office-footer',
                    ]),
                    'layout'   => false,
                ],
            ],
            [$html, $templates->name, $templates->params],
        );
    }

    #[Test]
    public function drawsAFormatWithoutATemplateAsPlainMarkup(): void
    {
        static::assertSame(
            "<address class=\"contact contact--header\">\n"
                . "<a class=\"contact__telephone\" href=\"tel:+61394280228\">03 9428 0228</a>\n</address>",
            self::contact()->render(self::office(), 'header'),
        );
    }

    #[Test]
    public function drawsTheFormatAContactBlockChose(): void
    {
        static::assertSame(
            [true, true],
            [
                str_contains(self::contact()->render(self::office(), ['format' => 'header']), 'contact--header'),
                str_contains(
                    self::contact()->render(self::office(), ['format' => 'custom', 'part' => ['map']]),
                    '<a class="contact__map"',
                ),
            ],
        );
    }

    #[Test]
    public function givesTheBusinessItsOfficesAndFormats(): void
    {
        $contact = self::contact();

        static::assertSame(
            ['Studio Esteta', 'k3f9q2', 'footer'],
            [
                $contact->business()->name,
                $contact->offices()->primary()?->id,
                $contact->formats()->get('footer')?->name,
            ],
        );
    }

    #[Test]
    public function saysWhichFormatsThereAreWhenAskedForOneThatIsnt(): void
    {
        $this->expectException(UnknownFormatException::class);
        $this->expectExceptionMessage(
            'There is no contact format "sidebar". Formats are full, address, compact, footer, header; add one under'
                . ' contenir_contact.format.',
        );

        self::contact()->render(self::office(), 'sidebar');
    }

    #[Test]
    public function withoutATemplateRendererEveryFormatIsPlainMarkup(): void
    {
        static::assertStringStartsWith(
            '<address class="contact contact--footer">',
            self::contact()->render(self::office(), 'footer'),
        );
    }
}
