<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomTheme\Module\ArticleXPro\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        $params = $data['params'];
        $helper = $this->getHelperFactory()->getHelper('ArticleXProHelper');

        $data['items'] = $helper->getArticles($params, $this->app, $this->input);
        $data['moduleId'] = 'jt-articlex-pro-' . (int) $this->module->id;
        $data['headingLevel'] = $helper->sanitizeHeadingLevel((string) $params->get('heading_level', 'h3'));
        $data['ratioClass'] = $helper->sanitizeRatioClass((string) $params->get('image_ratio', 'ratio-16x9'));
        $data['columns'] = max(1, min(4, (int) $params->get('columns', 3)));

        return $data;
    }
}
