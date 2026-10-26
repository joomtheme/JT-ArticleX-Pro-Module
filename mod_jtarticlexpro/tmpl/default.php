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

/** @var array $items */
/** @var Joomla\Registry\Registry $params */

$moduleClass = htmlspecialchars((string) $params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$colMd = min(2, max(1, $columns));
?>
<div id="<?php echo htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8'); ?>" class="jt-articlex-pro jt-articlex-pro-default <?php echo $moduleClass; ?>">
    <?php if (!$items) : ?>
        <?php require __DIR__ . '/partials/empty.php'; ?>
    <?php else : ?>
        <div class="row row-cols-1 row-cols-md-<?php echo (int) $colMd; ?> row-cols-lg-<?php echo (int) $columns; ?> g-4">
            <?php foreach ($items as $item) : ?>
                <div class="col">
                    <?php require __DIR__ . '/partials/card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php require __DIR__ . '/partials/schema.php'; ?>
</div>
