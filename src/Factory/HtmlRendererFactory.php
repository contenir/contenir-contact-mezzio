<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Format\HtmlRenderer;
use Contenir\Contact\MapProvider;
use Contenir\Contact\Mezzio\SiteSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function is_string;
use function trim;

/**
 * The plain markup for formats without a template, linking maps with the
 * provider Site Settings > Maps chooses, and contenir_contact.map_text as
 * the map link's text.
 *
 * @api
 */
final class HtmlRendererFactory
{
    public const string MAP_TEXT = 'Map';

    /**
     * @throws ContainerExceptionInterface
     *
     * @mago-expect analysis:mixed-assignment The application's config is untyped; the text is checked here.
     */
    public function __invoke(ContainerInterface $container): HtmlRenderer
    {
        $text = Config::options($container)['map_text'] ?? null;
        $text = is_string($text) ? trim($text) : '';

        return new HtmlRenderer(
            MapProvider::fromSettings(SiteSettings::fromConfig(Config::application($container))),
            '' === $text ? self::MAP_TEXT : $text,
        );
    }
}
