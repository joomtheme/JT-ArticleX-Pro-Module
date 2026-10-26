# JT ArticleX PRO v1.0.19

Bootstrap-native article showcase and carousel module for Joomla 6.

## Goals

- Joomla 6 site module tested on 6.1.1, 6.1.4 and 6.2.0 with PHP 8.3.32
- Requires PHP 8.3+ and a site template providing Bootstrap 5 CSS
- Modern module structure with `services/provider.php`, namespace, `src/Dispatcher`, `src/Helper` and `tmpl`
- Bootstrap output only; no bundled CSS framework and no external carousel library
- Template override friendly layouts
- EN/TR language files, including `.sys.ini`
- Joomla Web Asset Manager registered frontend CSS
- Atum-compatible administrator fields and Cassiopeia-compatible frontend output

## Layouts

- Default Cards
- Carousel
- Grid
- Compact List
- Media List
- Featured + List
- Magazine

## Manifest

- Author: JoomTheme
- Website: https://joomtheme.com
- Support: support@joomtheme.com
- Extension: `mod_jtarticlexpro`
- License: GPL-2.0-or-later

## Notes for testing

Install through Joomla Administrator > System > Install > Extensions. Then create or edit a Site Module and choose **JT ArticleX PRO**.

The module keeps frontend output Bootstrap-native and template override friendly. The small packaged CSS file only provides the sizing rules that Bootstrap utilities do not cover and is registered through Joomla's Web Asset Manager.

## 1.0.19

- Fixed image-only filtering without string-literal binds or JSON-format-dependent SQL.
- Decode valid JSON, skip invalid/empty images and fill the requested count after filtering.
- Keep manual selection order and evaluate random ordering in one result stream.
- Match fallback image alt text to the selected image.
- Inject the Joomla database into the helper.
- Respect scheduled featured start/end times in filters and featured state.
- Log missing core carousel assets and render every article in a grid fallback.
- Keep autoplay disabled when its parameter is absent.
- Fix scoped minimum-width classes in List and Media List.
- Correct PHP requirements to 8.3+; validate Joomla 6.1.4 and 6.2.0.

Image filtering uses Joomla's database iterator and PHP JSON decoding for database portability. When enabled, it may examine more candidate rows than the requested count. Bootstrap CSS is provided by the active site template; the module does not bundle a framework.

## 1.0.18

- Made the Media List layout respect the configured one-to-four column count.
- Made Media List images use the configured 16:9, 4:3, 1:1 or 21:9 ratio.
- Preserved the horizontal media-row presentation when one column is selected.
- Added responsive one-column, two-column and desktop column transitions using Joomla's Bootstrap grid utilities.

## 1.0.17

- Corrected the WebAsset stylesheet URI so Joomla resolves it to `/media/mod_jtarticlexpro/css/site.css`.
- Restored the intended 16rem Media List thumbnail width on `sm` and larger screens.
- Kept Media List images fluid at full width on extra-small screens.

## 1.0.16

- Registered `media/joomla.asset.json` explicitly through Joomla's extension asset registry.
- Removed silent WebAsset fallback and broad asset-loading exception handling.
- Applied the image-only filter before the result limit so the requested article count is preserved.
- Normalized Joomla null publish dates before rendering metadata and JSON-LD.
- Improved absolute article and image URLs for Joomla installations in a subdirectory.
- Clarified Atum administrator and Cassiopeia frontend compatibility notes.

## 1.0.15

- Added the full GPL-2.0 license text to `LICENSE.txt` for release/JED checks.
- Added Joomla Web Asset Manager metadata and a small scoped frontend CSS file.
- Removed layout inline sizing styles for carousel controls, indicators, badges and thumbnails.
- Hardened article/category/tag/access `IN (...)` query filters with Joomla query array bindings.
- Improved carousel accessibility labels and safer autoplay default for new module instances.
- Escaped formatted date output in the shared meta partial.
- Updated package version metadata for the Joomla 6.1.1 maintenance release.

## 1.0.14

- Hardened JSON-LD output with safe JSON_HEX encoding flags.
- Normalized schema image URLs to absolute URLs.
- Added the manifest XML file to the installer file list.
- Cleaned README layout wording for the Joomla 6.1.1 release.

## 1.0.12

- Added JoomTheme and JED Reviews links to the module admin description area.
- Kept branding out of frontend output by default for a cleaner PRO module experience.

## 1.0.11

- Added GPL-compatible license headers to all PHP files for JED Checker compatibility.
- Added Joomla update server URL to the manifest.
- Added Joomla changelog URL to the manifest.

## 1.0.10

- Fixed Featured Badge rendering inside Bootstrap ratio wrappers by moving the badge outside the `.ratio` direct child flow.
- Added defensive sizing to the badge so Bootstrap ratio styles cannot stretch it.

## 1.0.6

- Removed the Preset field because it did not apply automatic frontend behaviour yet.
- Added mini thumbnails to the secondary articles in the Featured + List layout.
- Secondary Featured + List thumbnails follow the global Show Image setting and gracefully fall back to text-only when an article has no image.

## 1.0.5

- Added Media List layout for wider image/article rows.
- List is now tighter with smaller 4:3 thumbnails and no read more button.
- Added Link Category option, enabled by default.
- Category titles link to Joomla category pages across card, carousel, grid, featured, magazine, list and media list layouts.

## 1.0.2

- Carousel previous/next controls moved outside the slide content with Bootstrap utility classes.
- Carousel indicators moved below the carousel using `position-static` to prevent overlaying article title and read more links.
- Added Indicator Style option: Lines or Dots.
