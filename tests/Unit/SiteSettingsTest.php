<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Unit;

use Contenir\Contact\Mezzio\SiteSettings;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteSettingsTest extends TestCase
{
    #[Test]
    public function laysTheEnvironmentsSettingsOverTheSites(): void
    {
        $settings = SiteSettings::fromConfig([
            'site'     => ['title' => 'Studio Esteta', 'url' => 'https://studioesteta.com.au'],
            'settings' => ['site' => ['url' => 'http://www.studioesteta.local']],
        ]);

        static::assertSame(
            ['Studio Esteta', 'http://www.studioesteta.local'],
            [$settings['site']['title'] ?? null, $settings['site']['url'] ?? null],
        );
    }

    #[Test]
    public function withoutTheEnvironmentsSettingsTheConfigIsTheSettings(): void
    {
        static::assertSame(
            [['site' => ['title' => 'Studio Esteta']], ['settings' => 'none']],
            [
                SiteSettings::fromConfig(['site' => ['title' => 'Studio Esteta']]),
                SiteSettings::fromConfig(['settings' => 'none']),
            ],
        );
    }
}
