# contenir/contenir-contact-mezzio

The Mezzio adapter for [contenir/contenir-contact](https://github.com/contenir/contenir-contact): the business and its
offices from a Mezzio site's settings, a `contact()` view helper for templates, and contact formats drawn by the
site's own templates.

The settings are edited in the CMS, in Site Settings > Office details.

## Requirements

- PHP 8.3, 8.4 or 8.5
- Mezzio, with mezzio-template 2.10 or later
- laminas-view, for the `contact()` helper; other template engines take `Contenir\Contact\Mezzio\Contact` from the
  container

## Install

```bash
composer require contenir/contenir-contact-mezzio
```

laminas-component-installer adds `Contenir\Contact\Mezzio\ConfigProvider` to `config/config.php`.

## Settings

A Mezzio site's settings are its **top-level config**, where the CMS's `config/autoload/settings.global.php` merges,
with **`config['settings']` laid over it**, where the CMS writes `settings.local.php`. The business is read from
`contact.business` and its offices from `contact.office`; [contenir-contact's README](https://github.com/contenir/contenir-contact#reading-the-settings)
lists the keys.

## In templates

```php
<?php $contact = $this->contact() ?>

<?php $primary = $contact->offices()->primary() ?>
<?php if (null !== $primary?->telephone): ?>
    <a href="<?= $this->escapeHtmlAttr($primary->telephone->uri()) ?>"><?= $this->escapeHtml((string) $primary->telephone) ?></a>
<?php endif ?>

<?php foreach ($contact->offices() as $office): ?>
    <?= $contact->render($office, 'footer') ?>
<?php endforeach ?>

<?= $contact->render($contact->business(), 'legal') ?>
```

For a Contact block, pass its fields: they hold both the offices it chose and its format.

```php
<?php foreach ($contact->offices()->pick($block['fields']['office']) as $office): ?>
    <?= $contact->render($office, $block['fields']) ?>
<?php endforeach ?>
```

The business and offices are read once from the settings and shared: they hold nothing about a request.

## Formats

The package has three formats, `full`, `address` and `compact`. A site adds its own, or replaces these by name:

```php
return [
    'contenir_contact' => [
        'format'   => [
            'header' => ['part' => ['telephone']],
            'footer' => ['part' => ['name', 'address'], 'template' => 'app::partials/office-footer'],
            'legal'  => ['subject' => 'business', 'part' => ['legal_name', 'tax_id'], 'label' => 'Fine print'],
        ],
        'map_text' => 'Map',   // the map link's text in plain markup
    ],
];
```

A format with a `template` is drawn through the application's `TemplateRendererInterface`, given:

| Variable | Value |
|---|---|
| `$office` | The office, or null when drawing the business |
| `$business` | The business |
| `$format` | The format, with its parts, so one template can serve several formats |
| `$layout` | `false`, so laminas-view draws no layout around it |

A format without a template, or any format when the application has no template renderer, is drawn as plain markup:
an `<address>` with a BEM class per part, the telephone and email as links, and a map link with the provider chosen
in Site Settings > Maps. Asking for a format the site doesn't have throws `UnknownFormatException`, naming the
formats it does have.

## Services

| Service | What it is |
|---|---|
| `Contenir\Contact\Business` | The business, with its offices |
| `Contenir\Contact\Format\Formats` | The package's formats with the site's |
| `Contenir\Contact\Format\HtmlRenderer` | Plain markup for formats |
| `Contenir\Contact\Mezzio\Contact` | All three, for templates; what `contact()` returns |

## Development

The QA toolchain is [contenir/contenir-qa-tools](https://github.com/contenir/contenir-qa-tools).

```bash
composer check             # everything below
composer cs-check          # mago format --check && mago lint
composer static-analysis   # mago analyze
composer test              # unit suite
composer test-integration  # a real container and laminas-view
composer mutation-test     # Infection
```

## Licence

BSD-3-Clause. See [LICENSE.md](LICENSE.md).
