# Pragmatic Social Links

Craft CMS plugin that adds a custom field type called `Social Links` to manage social media URLs in a clean, reorderable table UI.

## What It Does

- Adds a new field type: `Social Links`
- Lets editors add multiple social links per entry
- Each row contains:
  - a social network selector
  - an optional custom title
  - a URL field
- Rows can be reordered with drag & drop
- Stores values as JSON
- Includes a plugin settings screen with frontend rendering examples

## Current Scope

The plugin currently includes:

- field registration
- field value normalization and serialization
- a table-like CP input UI
- a curated list of popular social networks
- plugin settings examples for:
  - text-only output
  - output with icons

## Requirements

- PHP `>=8.2`
- Craft CMS `^5.0`

## Installation

Install it in your Craft project as a local plugin or Composer package.

### Composer

```bash
composer require pragmatic/social-links-craftcms-plugin
```

Then install it from the Craft control panel or via console:

```bash
php craft plugin/install pragmatic-social-links
```

## Field Usage

1. Go to `Settings > Fields`
2. Create a new field
3. Select the field type `Social Links`
4. Add the field to an entry type
5. Edit an entry and add as many social rows as needed

Each row allows:

- selecting a social network
- customizing the displayed title
- entering the corresponding URL
- reordering rows later
- deleting unused rows

## Available Social Networks

The field ships with a curated built-in list ordered broadly by mainstream popularity, including:

`Facebook`, `YouTube`, `Instagram`, `TikTok`, `WeChat`, `WhatsApp`, `Messenger`, `Telegram`, `Snapchat`, `X / Twitter`, `LinkedIn`, `Pinterest`, `Reddit`, `Discord`, `Threads`, `Twitch`, `LINE`, `QQ`, `Weibo`, `Tumblr`, `Viber`, `Mastodon`, `Bluesky`, `Medium`, `Quora`, `Flickr`, `Vimeo`, `GitHub`, `GitLab`, `Dribbble`, `Behance`, `Spotify`, `SoundCloud`, `Xing`

## Accessing the Field in Twig

The field returns a normalized value object with helper methods.

Example:

```twig
{% set links = entry.yourSocialLinksFieldHandle ?? null %}

{% if links and not links.isEmpty() %}
  <ul>
    {% for item in links.formatted() %}
      <li>
        <strong>{{ item.title }}</strong>:
        <a href="{{ item.url }}">{{ item.url }}</a>
      </li>
    {% endfor %}
  </ul>
{% endif %}
```

## Twig Example: Text Only

```twig
{% set links = entry.yourSocialLinksFieldHandle ?? null %}

{% if links and not links.isEmpty() %}
  <ul>
    {% for item in links.formatted() %}
      <li>
        <strong>{{ item.title }}</strong>:
        <a href="{{ item.url }}">{{ item.url }}</a>
      </li>
    {% endfor %}
  </ul>
{% endif %}
```

## Twig Example: With Icons

```twig
{% set links = entry.yourSocialLinksFieldHandle ?? null %}

{% if links and not links.isEmpty() %}
  <div class="social-links social-links--icons">
    {% for item in links.formatted('icons') %}
      <a href="{{ item.url }}" class="social-links__item">
        {{ item.icon|raw }}
        <span>{{ item.title }}</span>
      </a>
    {% endfor %}
  </div>
{% endif %}
```

## Plugin Settings

The plugin adds a settings page under:

`Settings > Plugins > Pragmatic Social Links`

This screen currently includes:

- rendering examples
- optional code example visibility
- default example variant selection

## Project Structure

```text
src/
  fields/
    SocialLinksField.php
  models/
    Settings.php
    SocialLinkItem.php
    SocialLinksFieldValue.php
  templates/
    fields/
      input.twig
      settings.twig
    settings.twig
  PragmaticSocialLinks.php
  icon.svg
```

## Notes

- Values are stored in a JSON column.
- The current icon set uses lightweight generated SVG placeholders.
- If you want official brand icons, that can be added in a future iteration.
- The plugin is structured to be easy to extend with validation, custom network settings, or frontend helpers.

## Development Status

Implemented as of August 24, 2026:

- plugin bootstrap
- Craft field type registration
- CP field UI
- settings page
- Twig-oriented output examples

Recommended next improvements:

- URL validation per network
- optional “open in new tab” support
- optional custom label per row
- official SVG icon set
- dedicated Twig extension or variable helper

## License

MIT
