<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\View;

use Contenir\Contact\Format\EmailEscaper;

/**
 * The escapeEmail() view helper: an email address as entities, and its mailto: link.
 *
 *     <a href="<?= $this->escapeEmail()->href($address) ?>"><?= $this->escapeEmail()->text($address) ?></a>
 *
 * @api
 */
final readonly class EscapeEmailHelper
{
    private EmailEscaper $escaper;

    public function __construct()
    {
        $this->escaper = new EmailEscaper();
    }

    public function __invoke(): EmailEscaper
    {
        return $this->escaper;
    }
}
