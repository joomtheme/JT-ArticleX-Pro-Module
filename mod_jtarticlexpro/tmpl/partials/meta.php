<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$metaClass = $metaClass ?? 'small text-body-secondary mb-2 d-flex flex-wrap gap-2';
?>
<?php if ($showCategory || $showDate || $showAuthor) : ?>
    <div class="<?php echo htmlspecialchars($metaClass, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if ($showCategory && $item->category_title !== '') : ?>
            <?php if ($linkCategory && $item->category_link !== '') : ?>
                <a href="<?php echo htmlspecialchars($item->category_link, ENT_QUOTES, 'UTF-8'); ?>" class="link-secondary text-decoration-none"><?php echo htmlspecialchars($item->category_title, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php else : ?>
                <span><?php echo htmlspecialchars($item->category_title, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($showDate && $item->date !== '') : ?>
            <time datetime="<?php echo htmlspecialchars($item->date, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC3')), ENT_QUOTES, 'UTF-8'); ?></time>
        <?php endif; ?>
        <?php if ($showAuthor && $item->author !== '') : ?>
            <span><?php echo htmlspecialchars($item->author, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
