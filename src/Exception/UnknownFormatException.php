<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Exception;

use InvalidArgumentException;

use function implode;
use function sprintf;

/**
 * A template asks for a format the site doesn't have.
 *
 * @api
 */
final class UnknownFormatException extends InvalidArgumentException
{
    /**
     * @param list<string> $known
     */
    public static function named(string $name, array $known): self
    {
        return new self(sprintf(
            'There is no contact format "%s". Formats are %s; add one under contenir_contact.format.',
            $name,
            implode(', ', $known),
        ));
    }
}
