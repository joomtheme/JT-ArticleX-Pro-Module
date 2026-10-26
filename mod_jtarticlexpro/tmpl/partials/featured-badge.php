<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

if (empty($showFeaturedBadge) || empty($item)) {
    return;
}

$badgeType = (string) $params->get('featured_badge_type', 'category');
$badgeText = '';

if ($badgeType === 'featured') {
    $badgeText = \Joomla\CMS\Language\Text::_('MOD_JTARTICLEXPRO_FEATURED_BADGE_FEATURED');
} elseif ($badgeType === 'custom') {
    $badgeText = trim((string) $params->get('featured_badge_text', ''));
} elseif (!empty($item->category_title)) {
    $badgeText = (string) $item->category_title;
}

if ($badgeText === '') {
    return;
}

$badgeStyle = preg_replace('/[^a-z0-9-]/i', '', (string) $params->get('featured_badge_style', 'primary'));
$allowedStyles = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark', 'light'];

if (!in_array($badgeStyle, $allowedStyles, true)) {
    $badgeStyle = 'primary';
}
?>
<span class="position-absolute top-0 start-0 m-3 badge jt-articlex-pro-badge text-bg-<?php echo htmlspecialchars($badgeStyle, ENT_QUOTES, 'UTF-8'); ?>">
    <?php echo htmlspecialchars($badgeText, ENT_QUOTES, 'UTF-8'); ?>
</span>
