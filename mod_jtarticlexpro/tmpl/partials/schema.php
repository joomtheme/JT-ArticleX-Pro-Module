<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

if (!(bool) $params->get('show_schema', 0) || !$items) {
    return;
}

$schemaItems = [];

foreach ($items as $item) {
    $entry = [
        '@type' => 'Article',
        'headline' => $item->title,
        'url' => $item->absolute_link,
        'datePublished' => $item->date,
    ];

    if ($item->absolute_image !== '') {
        $entry['image'] = $item->absolute_image;
    }

    if ($item->author !== '') {
        $entry['author'] = [
            '@type' => 'Person',
            'name' => $item->author,
        ];
    }

    $schemaItems[] = $entry;
}

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'itemListElement' => array_map(
        static function (array $entry, int $index): array {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $entry,
            ];
        },
        $schemaItems,
        array_keys($schemaItems)
    ),
];
?>
<script type="application/ld+json"><?php echo json_encode(
    $schema,
    JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
); ?></script>
