<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\View;

use Contenir\Contact\Mezzio\Contact;

/**
 * The contact() view helper: the business, its offices and their formats.
 *
 *     <?= $this->contact()->render($office, 'footer') ?>
 *
 * @api
 */
final readonly class ContactHelper
{
    public function __construct(
        private Contact $contact,
    ) {}

    public function __invoke(): Contact
    {
        return $this->contact;
    }
}
