<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_jtarticlexpro');
$wa->useStyle('mod_jtarticlexpro.site');
