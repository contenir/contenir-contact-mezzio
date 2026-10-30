<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\Unit\View;

use Contenir\Contact\Format\EmailEscaper;
use Contenir\Contact\Mezzio\Factory\EscapeEmailHelperFactory;
use Contenir\Contact\Mezzio\View\EscapeEmailHelper;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class EscapeEmailHelperTest extends TestCase
{
    #[Test]
    public function theFactoryMakesTheHelper(): void
    {
        static::assertInstanceOf(EscapeEmailHelper::class, (new EscapeEmailHelperFactory())());
    }

    #[Test]
    public function theHelperGivesTheSameEscaperEachCall(): void
    {
        $helper = new EscapeEmailHelper();

        static::assertSame($helper(), $helper());
    }

    #[Test]
    public function theHelpersEscaperWritesAsTheCoreOneDoes(): void
    {
        $escaper = (new EscapeEmailHelper())();

        static::assertSame(
            [(new EmailEscaper())->text('a@b.au'), (new EmailEscaper())->href('a@b.au')],
            [$escaper->text('a@b.au'), $escaper->href('a@b.au')],
        );
    }
}
