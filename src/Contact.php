<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio;

use Contenir\Contact\Business;
use Contenir\Contact\Exception\InvalidFormatException;
use Contenir\Contact\Format\Format;
use Contenir\Contact\Format\Formats;
use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\Mezzio\Exception\UnknownFormatException;
use Contenir\Contact\Office;
use Contenir\Contact\Offices;
use Mezzio\Template\TemplateRendererInterface;

use function array_map;
use function is_string;

/**
 * The business and its offices for templates, and their formats drawn by the
 * site's own templates or as plain markup.
 *
 * Built once from the site's settings and shared, so it holds nothing about
 * a request.
 *
 * @api
 */
final readonly class Contact
{
    public function __construct(
        private Business $business,
        private Formats $formats,
        private HtmlRenderer $html,
        private ?TemplateRendererInterface $templates = null,
    ) {}

    public function business(): Business
    {
        return $this->business;
    }

    public function formats(): Formats
    {
        return $this->formats;
    }

    public function offices(): Offices
    {
        return $this->business->offices;
    }

    /**
     * An office or the business in a format: a format's name ("footer"), a
     * Contact block's fields (['format' => 'footer'] or ['format' => 'custom',
     * 'part' => […]]), or a Format.
     *
     * A format with a template is drawn by it, given the office (null for
     * the business), the business, the format and layout false; any other
     * as plain markup.
     *
     * @param string|array<array-key, mixed>|Format $format
     *
     * @throws UnknownFormatException When a name isn't one of the site's formats.
     * @throws InvalidFormatException When a Contact block's custom choice names a part that doesn't exist.
     */
    public function render(Office|Business $subject, string|array|Format $format): string
    {
        $format = $this->format($format);
        if (null === $format->template || '' === $format->template || null === $this->templates) {
            return $this->html->render($subject, $format);
        }

        return $this->templates->render($format->template, [
            'office'   => $subject instanceof Office ? $subject : null,
            'business' => $this->business,
            'format'   => $format,
            'layout'   => false,
        ]);
    }

    /**
     * @param string|array<array-key, mixed>|Format $format
     *
     * @throws UnknownFormatException
     * @throws InvalidFormatException
     */
    private function format(string|array|Format $format): Format
    {
        if (! is_string($format)) {
            return $format instanceof Format ? $format : $this->formats->chosen($format);
        }

        return (
            $this->formats->get($format) ?? throw UnknownFormatException::named(
                $format,
                array_map(static fn(Format $known): string => $known->name, $this->formats->all()),
            )
        );
    }
}
