# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- The `escapeEmail()` view helper, giving contenir-contact's `EmailEscaper`.

## [0.1.0] - 2026-10-23

### Added

- The business and its offices from a Mezzio site's settings: the top-level config with `config['settings']` laid
  over it.
- `Contact` and the `contact()` view helper: the business, its offices and formats, with `render()` drawing a format
  by name, from a Contact block's fields, or as given.
- Formats from `contenir_contact.format`, drawn by the site's template through `TemplateRendererInterface` when a
  format names one, and as plain markup otherwise, with maps linked by Site Settings > Maps' provider.
