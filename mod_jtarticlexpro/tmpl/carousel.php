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
use Joomla\CMS\Log\Log;
use Joomla\CMS\WebAsset\Exception\UnknownAssetException;

require_once __DIR__ . '/partials/assets.php';

try {
    $app->getDocument()->getWebAssetManager()->useScript('bootstrap.carousel');
} catch (UnknownAssetException $e) {
    Log::add('Bootstrap carousel asset unavailable; displaying the article grid instead.', Log::WARNING, 'mod_jtarticlexpro');
    require __DIR__ . '/grid.php';

    return;
}

$moduleClass = htmlspecialchars((string) $params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$itemsPerSlide = max(1, min(4, (int) $params->get('items_per_slide', 1)));
$slideColumns = max(1, min($columns, $itemsPerSlide));
$colMd = min(2, $slideColumns);
$slides = array_chunk($items, $itemsPerSlide);
$autoplay = (bool) $params->get('autoplay', 0);
$interval = max(1000, min(30000, (int) $params->get('interval', 5000)));
$pause = (bool) $params->get('pause_hover', 1) ? 'hover' : 'false';
$showControls = (bool) $params->get('show_controls', 1) && count($slides) > 1;
$showIndicators = (bool) $params->get('show_indicators', 1) && count($slides) > 1;
$controlsPosition = (string) $params->get('controls_position', 'bottom');
$indicatorStyle = (string) $params->get('indicator_style', 'lines');
$indicatorButtonClass = $indicatorStyle === 'dots' ? 'jt-articlex-pro-indicator jt-articlex-pro-indicator-dot mx-1' : 'jt-articlex-pro-indicator mx-1';
$controlButtonClass = 'btn btn-outline-primary btn-sm rounded-circle flex-shrink-0 d-inline-flex align-items-center justify-content-center p-0 jt-articlex-pro-control';
$previousLabel = htmlspecialchars(Text::_('JPREVIOUS'), ENT_QUOTES, 'UTF-8');
$nextLabel = htmlspecialchars(Text::_('JNEXT'), ENT_QUOTES, 'UTF-8');
$carouselLabel = htmlspecialchars(Text::_('MOD_JTARTICLEXPRO_CAROUSEL_LABEL'), ENT_QUOTES, 'UTF-8');
$escapedModuleId = htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8');
$ariaLive = $autoplay ? 'off' : 'polite';

$previousIcon = '<svg class="d-block" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M10.5 3.5 6 8l4.5 4.5-1.4 1.4L3.2 8l5.9-5.9 1.4 1.4z"/></svg>';
$nextIcon = '<svg class="d-block" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M5.5 3.5 10 8l-4.5 4.5 1.4 1.4L12.8 8 6.9 2.1 5.5 3.5z"/></svg>';
?>
<div id="<?php echo $escapedModuleId; ?>" class="jt-articlex-pro jt-articlex-pro-carousel carousel slide <?php echo $moduleClass; ?>" <?php echo $autoplay ? 'data-bs-ride="carousel"' : ''; ?> data-bs-interval="<?php echo (int) $interval; ?>" data-bs-pause="<?php echo htmlspecialchars($pause, ENT_QUOTES, 'UTF-8'); ?>" role="region" aria-roledescription="carousel" aria-label="<?php echo $carouselLabel; ?>">
    <?php if (!$items) : ?>
        <?php require __DIR__ . '/partials/empty.php'; ?>
    <?php else : ?>
        <?php if ($controlsPosition === 'outside') : ?>
            <div class="d-flex align-items-center gap-2">
                <?php if ($showControls) : ?>
                    <button class="<?php echo $controlButtonClass; ?>" type="button" data-bs-target="#<?php echo $escapedModuleId; ?>" data-bs-slide="prev" aria-controls="<?php echo $escapedModuleId; ?>" aria-label="<?php echo $previousLabel; ?>">
                        <?php echo $previousIcon; ?>
                    </button>
                <?php endif; ?>

                <div class="carousel-inner flex-grow-1" aria-live="<?php echo $ariaLive; ?>">
                    <?php foreach ($slides as $index => $slide) : ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo htmlspecialchars(Text::sprintf('MOD_JTARTICLEXPRO_SLIDE_LABEL', $index + 1), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="row row-cols-1 row-cols-md-<?php echo (int) $colMd; ?> row-cols-lg-<?php echo (int) $slideColumns; ?> g-4">
                                <?php foreach ($slide as $item) : ?>
                                    <div class="col">
                                        <?php require __DIR__ . '/partials/card.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($showControls) : ?>
                    <button class="<?php echo $controlButtonClass; ?>" type="button" data-bs-target="#<?php echo $escapedModuleId; ?>" data-bs-slide="next" aria-controls="<?php echo $escapedModuleId; ?>" aria-label="<?php echo $nextLabel; ?>">
                        <?php echo $nextIcon; ?>
                    </button>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="carousel-inner" aria-live="<?php echo $ariaLive; ?>">
                <?php foreach ($slides as $index => $slide) : ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo htmlspecialchars(Text::sprintf('MOD_JTARTICLEXPRO_SLIDE_LABEL', $index + 1), ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="row row-cols-1 row-cols-md-<?php echo (int) $colMd; ?> row-cols-lg-<?php echo (int) $slideColumns; ?> g-4">
                            <?php foreach ($slide as $item) : ?>
                                <div class="col">
                                    <?php require __DIR__ . '/partials/card.php'; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($showControls || $showIndicators) : ?>
            <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
                <?php if ($showControls && $controlsPosition !== 'outside') : ?>
                    <button class="<?php echo $controlButtonClass; ?>" type="button" data-bs-target="#<?php echo $escapedModuleId; ?>" data-bs-slide="prev" aria-controls="<?php echo $escapedModuleId; ?>" aria-label="<?php echo $previousLabel; ?>">
                        <?php echo $previousIcon; ?>
                    </button>
                <?php endif; ?>

                <?php if ($showIndicators) : ?>
                    <div class="carousel-indicators position-static justify-content-center gap-1 my-0 mx-0">
                        <?php foreach ($slides as $index => $slide) : ?>
                            <button class="<?php echo $indicatorButtonClass; ?><?php echo $index === 0 ? ' active' : ''; ?>" type="button" data-bs-target="#<?php echo $escapedModuleId; ?>" data-bs-slide-to="<?php echo (int) $index; ?>" <?php echo $index === 0 ? 'aria-current="true"' : ''; ?> aria-label="<?php echo htmlspecialchars(Text::sprintf('MOD_JTARTICLEXPRO_SLIDE_LABEL', $index + 1), ENT_QUOTES, 'UTF-8'); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($showControls && $controlsPosition !== 'outside') : ?>
                    <button class="<?php echo $controlButtonClass; ?>" type="button" data-bs-target="#<?php echo $escapedModuleId; ?>" data-bs-slide="next" aria-controls="<?php echo $escapedModuleId; ?>" aria-label="<?php echo $nextLabel; ?>">
                        <?php echo $nextIcon; ?>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php require __DIR__ . '/partials/schema.php'; ?>
</div>
