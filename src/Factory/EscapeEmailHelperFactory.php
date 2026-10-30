<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Factory;

use Contenir\Contact\Mezzio\View\EscapeEmailHelper;

/**
 * @api
 */
final class EscapeEmailHelperFactory
{
    public function __invoke(): EscapeEmailHelper
    {
        return new EscapeEmailHelper();
    }
}
