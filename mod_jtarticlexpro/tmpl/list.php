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
$showImage = (bool) $params->get('show_image', 1);
$showIntro = (bool) $params->get('show_intro', 1);
$showCategory = (bool) $params->get('show_category', 1);
$linkCategory = (bool) $params->get('link_category', 1);
$showDate = (bool) $params->get('show_date', 1);
$showAuthor = (bool) $params->get('show_author', 0);
$infoPosition = (string) $params->get('info_position', 'below');
?>
<div id="<?php echo htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8'); ?>" class="jt-articlex-pro jt-articlex-pro-list <?php echo $moduleClass; ?>">
    <?php if (!$items) : ?>
        <?php require __DIR__ . '/partials/empty.php'; ?>
    <?php else : ?>
        <div class="list-group list-group-flush">
            <?php foreach ($items as $item) : ?>
                <article class="list-group-item px-0 py-3">
                    <div class="d-flex align-items-start gap-3">
                        <?php if ($showImage && $item->image !== '') : ?>
                            <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="flex-shrink-0 text-decoration-none jt-articlex-pro-thumb-sm" aria-label="<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="ratio ratio-4x3 bg-body-tertiary rounded overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item->image_alt, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                                </span>
                            </a>
                        <?php endif; ?>

                        <div class="flex-grow-1 jt-articlex-pro-min-w-0">
                            <?php if ($infoPosition === 'above') : ?>
                                <?php $metaClass = 'small text-body-secondary mb-1 d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                            <?php endif; ?>

                            <<?php echo $headingLevel; ?> class="h6 mb-1">
                                <a href="<?php echo htmlspecialchars($item->link, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </<?php echo $headingLevel; ?>>

                            <?php if ($infoPosition !== 'above') : ?>
                                <?php $metaClass = 'small text-body-secondary d-flex flex-wrap gap-2'; require __DIR__ . '/partials/meta.php'; ?>
                            <?php endif; ?>

                            <?php if ($showIntro && $item->intro !== '') : ?>
                                <p class="small text-body-secondary mb-0 mt-1"><?php echo htmlspecialchars($item->intro, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php require __DIR__ . '/partials/schema.php'; ?>
</div>
