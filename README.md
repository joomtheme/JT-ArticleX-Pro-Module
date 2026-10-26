# JT ArticleX PRO

**JT ArticleX PRO v1.0.19** is a Bootstrap-native article showcase module for Joomla 6, built by [JoomTheme](https://joomtheme.com).

Display Joomla articles as cards, a carousel, a compact list, a media list, a featured section or a magazine layout. The module uses Joomla's core Bootstrap carousel and Web Asset Manager, with a small scoped stylesheet and no bundled CSS framework or external slider library.

[Download v1.0.19](https://github.com/joomtheme/JT-ArticleX-Pro-Module/releases/download/v1.0.19/mod_jtarticlexpro_v1.0.19.zip) · [Release notes](https://github.com/joomtheme/JT-ArticleX-Pro-Module/releases/tag/v1.0.19) · [Report an issue](https://github.com/joomtheme/JT-ArticleX-Pro-Module/issues)

## Requirements and compatibility

| Requirement | Details |
| --- | --- |
| CMS | Joomla 6 |
| PHP | 8.3 or newer |
| Site template | Must provide Bootstrap 5 CSS; tested with Cassiopeia |
| Administrator template | Tested with Atum |
| Languages | English and Turkish |

Version 1.0.19 was tested on **Joomla 6.1.1, 6.1.4 and 6.2.0**, with PHP 8.3.32 and MariaDB 11.4. Atum is the administrator template; Cassiopeia provides the tested frontend. Third-party templates and PostgreSQL have not been tested.

## Layouts

| Layout | Use |
| --- | --- |
| Default Cards / Grid | Responsive article cards |
| Carousel | Core Bootstrap slides with configurable controls and indicators |
| Compact List | Small thumbnails and concise article details |
| Media List | One to four columns with selectable image ratios |
| Featured + List | One large story with a compact secondary list |
| Magazine | A lead story with smaller article cards |

## Features

- Filter by category, subcategory, tags, manual article IDs and featured state.
- Respect article access levels, publication dates and scheduled featured dates.
- Choose article count and ordering, exclude the current article and show only articles with images.
- Configure images, image ratios, headings, category links, dates, authors, intro text and read-more links where supported by the layout.
- Customize carousel autoplay, interval, controls and indicators.
- Configure image badges and metadata placement.
- Optionally output JSON-LD article lists.
- Use Joomla template overrides, namespaced helpers and dispatchers, dependency injection and Web Asset Manager assets.
- Receive extension updates and changelog information through Joomla's update system.

## Install or update

1. Download **mod_jtarticlexpro_v1.0.19.zip** from the [release](https://github.com/joomtheme/JT-ArticleX-Pro-Module/releases/tag/v1.0.19).
2. In Joomla Administrator, open **System → Install → Extensions** and upload the ZIP.
3. Open **Content → Site Modules** and create or edit **JT ArticleX PRO**.
4. Select content, choose a layout and template position, assign the module to menu items, and publish it.

Installing over an existing version updates the module while preserving its saved settings. Use the installable ZIP attached to the release, rather than GitHub's automatically generated source archives.

SHA-256:

```text
c0a8dae1191f960eac4b7a9cb6b7f62dc70849fbe931f45a091e91319dcea626
```

## What's new in 1.0.19

- Fixed PHP errors in image-only filtering and removed dependence on JSON whitespace or key escaping.
- Skip malformed or empty image values and fill the requested article count after filtering.
- Preserve manual article order and process random ordering in one result stream.
- Pair fallback images with their own alt text.
- Inject the Joomla database into the helper.
- Respect scheduled featured start and end dates in filtering and displayed featured state.
- Log an unavailable core carousel asset and display every article in a grid fallback.
- Keep autoplay disabled when the parameter is absent and correct scoped layout sizing classes.
- Clarify Joomla 6's PHP 8.3+ requirement.

Versioned update metadata and changelog entries are maintained in [`updates/update.xml`](updates/update.xml) and [`updates/changelog.xml`](updates/changelog.xml).

## Screenshots

### Magazine

![Magazine layout](screenshots/magazine-layout.png)

### Featured + List

![Featured and list layout](screenshots/featured-list-layout.png)

### Carousel

![Carousel layout](screenshots/carousel-layout.png)

## Development

| Path | Contents |
| --- | --- |
| `mod_jtarticlexpro/` | Editable module sources, manifest, layouts, assets and translations |
| `mod_jtarticlexpro_v1.0.19.zip` | Installable release package |
| `tools/build-package.py` | Deterministic ZIP builder |
| `tests/` | Joomla integration tests, browser checks and fixture setup |
| `updates/` | Joomla update-server and changelog XML |

Build the module package from the repository root:

```sh
python3 tools/build-package.py
```

The ZIP has the module manifest at its root and excludes tests and development tools. Unchanged module sources produce an identical archive. Published release packages must remain unchanged so their SHA-256 values stay valid.

See [`tests/README.md`](tests/README.md) for isolated-site test instructions. Validation covered **34 integration assertions per tested Joomla version**, all seven layouts, carousel navigation, CSS delivery, JSON-LD output and the Atum module edit form. This is not a claim that every configuration or third-party template has been tested.

Image-only filtering decodes JSON through PHP and iterates the Joomla database result until enough usable images are found. It may inspect more candidate rows than the requested display count.

## License and support

Licensed under **GPL-2.0-or-later**. See [`LICENSE`](LICENSE) and the module's `LICENSE.txt`.

[JoomTheme](https://joomtheme.com) · [GitHub issues](https://github.com/joomtheme/JT-ArticleX-Pro-Module/issues) · [support@joomtheme.com](mailto:support@joomtheme.com)
