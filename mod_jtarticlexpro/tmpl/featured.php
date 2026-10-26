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

require_once __DIR__ . '/partials/assets.php';

$moduleClass = htmlspecialchars((string) $params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$main = $items[0] ?? null;
$secondaryItems = array_slice($items, 1);
$showImage = (bool) $params->get('show_image', 1);
$showIntro = (bool) $params->get('show_intro', 1);
$showCategory = (bool) $params->get('show_category', 1);
$linkCategory = (bool) $params->get('link_category', 1);
$showDate = (bool) $params->get('show_date', 1);
$showAuthor = (bool) $params->get('show_author', 0);
$showReadmore = (bool) $params->get('show_readmore', 1);
$infoPosition = (string) $params->get('info_position', 'below');
$showFeaturedBadge = (bool) $params->get('show_featured_badge', 1);
?>
<div id="<?php echo htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8'); ?>" class="jt-articlex-pro jt-articlex-pro-featured <?php echo $moduleClass; ?>">
    <?php if (!$items || !$main) : ?>
        <?php require __DIR__ . '/partials/empty.php'; ?>
    <?php else : ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-lg-7">
                <article class="card h-100">
                    <?php if ($showImage && $main->image !== '') : ?>
                        <?php $item = $main; ?>
                        <a href="<?php echo htmlspecialchars($main->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                            <span class="d-block position-relative">
                                <span class="ratio <?php echo htmlspecialchars($ratioClass, ENT_QUOTES, 'UTF-8'); ?> bg-body-tertiary overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($main->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($main->image_alt, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                                </span>
                                <?php require __DIR__ . '/partials/featured-badge.php'; ?>
                            </span>
                        </a>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php $item = $main; ?>
                        <?php if ($infoPosition === 'above') : ?>
                            <?php $metaClass = 'small text-body-secondary mb-2 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                        <?php endif; ?>

                        <<?php echo $headingLevel; ?> class="h3 card-title">
                            <a href="<?php echo htmlspecialchars($main->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($main->title, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </<?php echo $headingLevel; ?>>

                        <?php if ($infoPosition !== 'above') : ?>
                            <?php $metaClass = 'small text-body-secondary mb-2 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                        <?php endif; ?>

                        <?php if ($showIntro && $main->intro !== '') : ?>
                            <p class="card-text text-body-secondary"><?php echo htmlspecialchars($main->intro, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>

                        <?php if ($showReadmore) : ?>
                            <a href="<?php echo htmlspecialchars($main->link, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                                <?php echo htmlspecialchars(Text::_('MOD_JTARTICLEXPRO_READ_MORE'), ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            </div>

            <div class="col-12 col-lg-5">
                <div class="vstack gap-3">
                    <?php foreach ($secondaryItems as $item) : ?>
                        <article class="card">
                            <div class="card-body p-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <?php if ($showImage && $item->image !== '') : ?>
                                        <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="flex-shrink-0 text-decoration-none jt-articlex-pro-thumb-featured">
                                            <span class="ratio ratio-4x3 rounded overflow-hidden bg-body-tertiary">
                                                <img src="<?php echo htmlspecialchars($item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->image_alt, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                                            </span>
                                        </a>
                                    <?php endif; ?>

                                    <div class="flex-grow-1 jt-articlex-pro-min-w-0">
                                        <?php if ($infoPosition === 'above') : ?>
                                            <?php $metaClass = 'small text-body-secondary mb-1 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                                        <?php endif; ?>

                                        <h4 class="h6 mb-1">
                                            <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </h4>

                                        <?php if ($infoPosition !== 'above') : ?>
                                            <?php $metaClass = 'small text-body-secondary mb-0 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php require __DIR__ . '/partials/schema.php'; ?>
</div>
