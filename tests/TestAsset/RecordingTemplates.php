<?php

declare(strict_types=1);

namespace Contenir\Contact\Mezzio\Tests\TestAsset;

use Mezzio\Template\TemplatePath;
use Mezzio\Template\TemplateRendererInterface;
use Override;

/**
 * A template renderer that keeps what it was asked to draw and draws its
 * name, for version 2 or 3 of mezzio-template.
 */
final class RecordingTemplates implements TemplateRendererInterface
{
    public string $name = '';

    /**
     * @var array<array-key, mixed>|object
     */
    public array|object $params = [];

    #[Override]
    public function addDefaultParam(string $templateName, string $param, mixed $value): void {}

    /**
     * Version 2 of the interface has it; version 3 doesn't.
     */
    public function addPath(string $path, ?string $namespace = null): void {}

    /**
     * Version 2 of the interface has it; version 3 doesn't.
     *
     * @return list<TemplatePath>
     */
    public function getPaths(): array
    {
        return [];
    }

    #[Override]
    public function render(string $name, $params = []): string
    {
        $this->name   = $name;
        $this->params = $params;

        return "template:{$name}";
    }
}
