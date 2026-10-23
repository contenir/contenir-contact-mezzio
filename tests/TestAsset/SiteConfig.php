<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\TestAsset;

use function array_replace_recursive;

/**
 * A Mezzio site's config with its contact settings: the business, one
 * office, and whatever else a test lays over them.
 */
final class SiteConfig
{
    /**
     * @param array<array-key, mixed> $over
     *
     * @return array<array-key, mixed>
     */
    public static function make(array $over = []): array
    {
        return array_replace_recursive([
            'site'    => ['title' => 'Studio Esteta', 'locale' => 'en_AU', 'timezone' => 'Australia/Melbourne'],
            'contact' => [
                'business' => ['legal_name' => 'Studio Esteta Pty Ltd', 'telephone' => '+61 3 9428 0228'],
                'office'   => [
                    [
                        'id'          => 'k3f9q2',
                        'name'        => 'Melbourne studio',
                        'street'      => '402 Queens Parade',
                        'locality'    => 'Fitzroy North',
                        'region'      => 'VIC',
                        'postcode'    => '3068',
                        'geolocation' => ['latitude' => -37.7787, 'longitude' => 144.9874],
                    ],
                ],
            ],
        ], $over);
    }
}
