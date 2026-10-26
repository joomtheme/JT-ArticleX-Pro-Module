<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

require_once __DIR__ . '/partials/assets.php';

$moduleClass = htmlspecialchars((string) $params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$lead = $items[0] ?? null;
$sideLimit = (int) $columns >= 4 ? 4 : 2;
$sideItems = array_slice($items, 1, $sideLimit);
$gridItems = array_slice($items, 1 + $sideLimit);

$showImage = (bool) $params->get('show_image', 1);
$showCategory = (bool) $params->get('show_category', 1);
$linkCategory = (bool) $params->get('link_category', 1);
$showDate = (bool) $params->get('show_date', 1);
$showAuthor = (bool) $params->get('show_author', 0);
$infoPosition = (string) $params->get('info_position', 'below');
?>
<div id="<?php echo htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8'); ?>" class="jt-articlex-pro jt-articlex-pro-magazine <?php echo $moduleClass; ?>">
    <?php if (!$items || !$lead) : ?>
        <?php require __DIR__ . '/partials/empty.php'; ?>
    <?php else : ?>
        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-xl-6">
                <?php $item = $lead; $featuredBadgeCard = true; require __DIR__ . '/partials/card.php'; unset($featuredBadgeCard); ?>
            </div>

            <?php if ($sideItems) : ?>
                <div class="col-12 col-xl-6">
                    <div class="row row-cols-1 row-cols-sm-2 g-4 h-100">
                        <?php foreach ($sideItems as $item) : ?>
                            <div class="col">
                                <article class="card h-100">
                                    <?php if ($showImage && $item->image !== '') : ?>
                                        <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                            <span class="ratio <?php echo htmlspecialchars($ratioClass, ENT_QUOTES, 'UTF-8'); ?> bg-body-tertiary">
                                                <img src="<?php echo htmlspecialchars($item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->image_alt, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                                            </span>
                                        </a>
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <?php if ($infoPosition === 'above') : ?>
                                            <?php $metaClass = 'small text-body-secondary mb-2 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                                        <?php endif; ?>

                                        <<?php echo $headingLevel; ?> class="h5 card-title mb-2">
                                            <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </<?php echo $headingLevel; ?>>

                                        <?php if ($infoPosition !== 'above') : ?>
                                            <?php $metaClass = 'small text-body-secondary mb-0 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($gridItems) : ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-<?php echo (int) $columns; ?> g-4 mt-0">
                <?php foreach ($gridItems as $item) : ?>
                    <div class="col">
                        <?php require __DIR__ . '/partials/card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php require __DIR__ . '/partials/schema.php'; ?>
</div>
