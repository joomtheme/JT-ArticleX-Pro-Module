<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$showImage = (bool) $params->get('show_image', 1);
$showIntro = (bool) $params->get('show_intro', 1);
$showCategory = (bool) $params->get('show_category', 1);
$linkCategory = (bool) $params->get('link_category', 1);
$showDate = (bool) $params->get('show_date', 1);
$showAuthor = (bool) $params->get('show_author', 0);
$showReadmore = (bool) $params->get('show_readmore', 1);
$infoPosition = (string) $params->get('info_position', 'below');
$showFeaturedBadge = !empty($featuredBadgeCard) && (bool) $params->get('show_featured_badge', 1);
?>
<article class="card h-100">
    <?php if ($showImage && $item->image !== '') : ?>
        <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
            <span class="d-block position-relative">
                <span class="ratio <?php echo htmlspecialchars($ratioClass, ENT_QUOTES, 'UTF-8'); ?> bg-body-tertiary overflow-hidden">
                    <img src="<?php echo htmlspecialchars($item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->image_alt, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                </span>
                <?php require __DIR__ . '/featured-badge.php'; ?>
            </span>
        </a>
    <?php endif; ?>

    <div class="card-body d-flex flex-column">
        <?php if ($infoPosition === 'above') : ?>
            <?php $metaClass = 'small text-body-secondary mb-2 d-flex flex-wrap gap-2'; require __DIR__ . '/meta.php'; ?>
        <?php endif; ?>

        <<?php echo $headingLevel; ?> class="h5 card-title mb-2">
            <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </<?php echo $headingLevel; ?>>

        <?php if ($infoPosition !== 'above') : ?>
            <?php $metaClass = 'small text-body-secondary mb-2 d-flex flex-wrap gap-2'; require __DIR__ . '/meta.php'; ?>
        <?php endif; ?>

        <?php if ($showIntro && $item->intro !== '') : ?>
            <p class="card-text text-body-secondary mb-3"><?php echo htmlspecialchars($item->intro, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if ($showReadmore) : ?>
            <p class="mt-auto mb-0">
                <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary">
                    <?php echo htmlspecialchars(Text::_('MOD_JTARTICLEXPRO_READ_MORE'), ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</article>
