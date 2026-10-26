<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jtarticlexpro
 *
 * @copyright   (C) 2026 JoomTheme. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomTheme\Module\ArticleXPro\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

class ArticleXProHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    public function getArticles(Registry $params, CMSApplicationInterface $app, Input $input): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $count = max(1, min(48, (int) $params->get('count', 6)));
        $now = Factory::getDate()->toSql();
        $nullDate = $db->getNullDate();
        $user = $app->getIdentity();
        $viewLevels = $this->normaliseIdList($user->getAuthorisedViewLevels());

        if (!$viewLevels) {
            $viewLevels = [1];
        }

        $articleIds = $this->normaliseIdList($params->get('article_ids', ''));
        $catIds = $this->normaliseIdList($params->get('catids', []));
        $tagIds = $this->normaliseIdList($params->get('tagids', []));
        $onlyWithImage = (bool) $params->get('only_with_image', 0);

        if ((bool) $params->get('use_current_category', 0)) {
            $currentCatId = $this->getCurrentCategoryId($input, $db);

            if ($currentCatId > 0) {
                $catIds[] = $currentCatId;
                $catIds = array_values(array_unique($catIds));
            }
        }

        if ($catIds && (bool) $params->get('include_subcategories', 1)) {
            $catIds = $this->expandCategoryIds($catIds, $db);
        }

        $articleViewLevelKeys = $query->bindArray($viewLevels, ParameterType::INTEGER);
        $categoryViewLevelKeys = $query->bindArray($viewLevels, ParameterType::INTEGER);

        $select = [
            $db->quoteName('a.id'),
            $db->quoteName('a.title'),
            $db->quoteName('a.alias'),
            $db->quoteName('a.catid'),
            $db->quoteName('a.introtext'),
            $db->quoteName('a.images'),
            $db->quoteName('a.created'),
            $db->quoteName('a.created_by'),
            $db->quoteName('a.created_by_alias'),
            $db->quoteName('a.modified'),
            $db->quoteName('a.publish_up'),
            $db->quoteName('a.hits'),
            $db->quoteName('a.featured'),
            $db->quoteName('fp.featured_up'),
            $db->quoteName('fp.featured_down'),
            $db->quoteName('a.language'),
            $db->quoteName('c.title', 'category_title'),
            $db->quoteName('c.alias', 'category_alias'),
            $db->quoteName('u.name', 'author_name'),
        ];

        $query->select($select)
            ->from($db->quoteName('#__content', 'a'))
            ->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
            ->join('LEFT', $db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by'))
            ->join('LEFT', $db->quoteName('#__content_frontpage', 'fp') . ' ON ' . $db->quoteName('fp.content_id') . ' = ' . $db->quoteName('a.id'))
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('c.published') . ' = 1')
            ->where($db->quoteName('a.access') . ' IN (' . implode(',', $articleViewLevelKeys) . ')')
            ->where($db->quoteName('c.access') . ' IN (' . implode(',', $categoryViewLevelKeys) . ')')
            ->where('(' . $db->quoteName('a.publish_up') . ' IS NULL OR ' . $db->quoteName('a.publish_up') . ' = :nullPublishUp OR ' . $db->quoteName('a.publish_up') . ' <= :nowPublishUp)')
            ->where('(' . $db->quoteName('a.publish_down') . ' IS NULL OR ' . $db->quoteName('a.publish_down') . ' = :nullPublishDown OR ' . $db->quoteName('a.publish_down') . ' >= :nowPublishDown)')
            ->bind(':nullPublishUp', $nullDate)
            ->bind(':nowPublishUp', $now)
            ->bind(':nullPublishDown', $nullDate)
            ->bind(':nowPublishDown', $now);

        if ((bool) $params->get('language_filter', 1)) {
            $language = $app->getLanguage()->getTag();
            $query->where('(' . $db->quoteName('a.language') . ' = :language OR ' . $db->quoteName('a.language') . ' = ' . $db->quote('*') . ')')
                ->bind(':language', $language);
        }

        if ($articleIds) {
            $articleIdKeys = $query->bindArray($articleIds, ParameterType::INTEGER);
            $query->where($db->quoteName('a.id') . ' IN (' . implode(',', $articleIdKeys) . ')');
        }

        if ($catIds) {
            $catIdKeys = $query->bindArray($catIds, ParameterType::INTEGER);
            $query->where($db->quoteName('a.catid') . ' IN (' . implode(',', $catIdKeys) . ')');
        }

        if ($tagIds) {
            $tagIdKeys = $query->bindArray($tagIds, ParameterType::INTEGER);
            $tagQuery = $db->getQuery(true)
                ->select($db->quoteName('tag_map.content_item_id'))
                ->from($db->quoteName('#__contentitem_tag_map', 'tag_map'))
                ->where($db->quoteName('tag_map.type_alias') . ' = ' . $db->quote('com_content.article'))
                ->where($db->quoteName('tag_map.tag_id') . ' IN (' . implode(',', $tagIdKeys) . ')');

            $query->where($db->quoteName('a.id') . ' IN (' . $tagQuery . ')');
        }

        $featured = (int) $params->get('featured', 0);

        if ($featured === 1 || $featured === -1) {
            $activeFeatured = '(' . $db->quoteName('a.featured') . ' = 1'
                . ' AND (' . $db->quoteName('fp.featured_up') . ' IS NULL OR ' . $db->quoteName('fp.featured_up') . ' <= :featuredUp)'
                . ' AND (' . $db->quoteName('fp.featured_down') . ' IS NULL OR ' . $db->quoteName('fp.featured_down') . ' >= :featuredDown))';
            $query->where(($featured === -1 ? 'NOT ' : '') . $activeFeatured)
                ->bind(':featuredUp', $now)
                ->bind(':featuredDown', $now);
        }

        if ((bool) $params->get('exclude_current', 0) && $input->getCmd('option') === 'com_content' && $input->getCmd('view') === 'article') {
            $currentId = (int) $input->getInt('id', 0);

            if ($currentId > 0) {
                $query->where($db->quoteName('a.id') . ' <> :currentArticleId')
                    ->bind(':currentArticleId', $currentId, ParameterType::INTEGER);
            }
        }

        $this->applyOrdering($query, $db, (string) $params->get('ordering', 'publish_up_desc'), (bool) $articleIds);

        if ($onlyWithImage) {
            // Decode JSON rather than matching its formatting or relying on vendor-specific SQL.
            // Iterate one ordered result, including for random ordering, until enough images exist.
            $db->setQuery($query);
            $rows = [];

            foreach ($db->getIterator() as $row) {
                if ($this->resolveImage($row, $params)['src'] === '') {
                    continue;
                }

                $rows[] = $row;

                if (!$articleIds && count($rows) >= $count) {
                    break;
                }
            }
        } else {
            $queryLimit = $articleIds ? max($count, count($articleIds)) : $count;
            $db->setQuery($query, 0, $queryLimit);
            $rows = $db->loadObjectList() ?: [];
        }

        if ($articleIds) {
            $rows = array_slice($this->sortRowsByManualIds($rows, $articleIds), 0, $count);
        }

        return $this->prepareItems($rows, $params, $nullDate, $now);
    }

    public function sanitizeHeadingLevel(string $level): string
    {
        return in_array($level, ['h2', 'h3', 'h4', 'h5'], true) ? $level : 'h3';
    }

    public function sanitizeRatioClass(string $ratio): string
    {
        return in_array($ratio, ['ratio-16x9', 'ratio-4x3', 'ratio-1x1', 'ratio-21x9'], true) ? $ratio : 'ratio-16x9';
    }

    private function prepareItems(array $rows, Registry $params, string $nullDate, string $now): array
    {
        $items = [];
        $introLimit = max(0, min(500, (int) $params->get('intro_limit', 120)));
        $onlyWithImage = (bool) $params->get('only_with_image', 0);

        foreach ($rows as $row) {
            $image = $this->resolveImage($row, $params);

            if ($onlyWithImage && $image['src'] === '') {
                continue;
            }

            $item = new \stdClass();
            $item->id = (int) $row->id;
            $item->title = (string) $row->title;
            $item->category_title = (string) $row->category_title;
            $item->category_link = $this->buildCategoryLink($row);
            $item->author = $row->created_by_alias ?: (string) $row->author_name;
            $publishUp = (string) ($row->publish_up ?? '');
            $item->date = $publishUp !== '' && $publishUp !== $nullDate
                ? $publishUp
                : (string) $row->created;
            $item->hits = (int) $row->hits;
            $item->featured = (int) ((int) $row->featured === 1
                && ($row->featured_up === null || $row->featured_up <= $now)
                && ($row->featured_down === null || $row->featured_down >= $now));
            $item->language = (string) $row->language;
            $item->image = $image['src'];
            $item->image_alt = $image['alt'] ?: $item->title;
            $item->intro = $this->truncateText($row->introtext, $introLimit);
            $item->link = $this->buildArticleLink($row);
            $item->absolute_link = $this->toAbsoluteUrl($item->link);
            $item->absolute_image = $this->toAbsoluteUrl($item->image);

            $items[] = $item;
        }

        return $items;
    }

    private function toAbsoluteUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return Uri::getInstance()->getScheme() . ':' . $url;
        }

        $basePath = Uri::root(true);

        if ($basePath !== '' && str_starts_with($url, $basePath . '/')) {
            $url = substr($url, strlen($basePath));
        }

        return Uri::root() . ltrim($url, '/');
    }

    private function buildArticleLink(object $row): string
    {
        $slug = (int) $row->id . ':' . (string) $row->alias;
        $catSlug = (int) $row->catid . ':' . (string) $row->category_alias;

        if (class_exists(RouteHelper::class)) {
            return Route::_(RouteHelper::getArticleRoute($slug, $catSlug, (string) $row->language));
        }

        return Route::_('index.php?option=com_content&view=article&id=' . (int) $row->id . '&catid=' . (int) $row->catid);
    }

    private function buildCategoryLink(object $row): string
    {
        $catSlug = (int) $row->catid . ':' . (string) $row->category_alias;

        if (class_exists(RouteHelper::class) && method_exists(RouteHelper::class, 'getCategoryRoute')) {
            return Route::_(RouteHelper::getCategoryRoute($catSlug, (string) $row->language));
        }

        return Route::_('index.php?option=com_content&view=category&id=' . (int) $row->catid);
    }

    private function resolveImage(object $row, Registry $params): array
    {
        $images = json_decode((string) $row->images, true);
        $source = (string) $params->get('image_source', 'intro');

        if (!is_array($images)) {
            return ['src' => '', 'alt' => ''];
        }

        $key = $source === 'fulltext' ? 'image_fulltext' : 'image_intro';
        $src = is_string($images[$key] ?? null) ? trim($images[$key]) : '';

        if ($source === 'auto' && $src === '') {
            $key = 'image_fulltext';
            $src = is_string($images[$key] ?? null) ? trim($images[$key]) : '';
        }

        $alt = is_string($images[$key . '_alt'] ?? null) ? trim($images[$key . '_alt']) : '';

        return [
            'src' => $src,
            'alt' => $alt,
        ];
    }

    private function truncateText(?string $html, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $html)) ?: '');

        if ($limit <= 0 || $text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $limit) {
            return rtrim(mb_substr($text, 0, $limit, 'UTF-8')) . '…';
        }

        if (!function_exists('mb_strlen') && strlen($text) > $limit) {
            return rtrim(substr($text, 0, $limit)) . '…';
        }

        return $text;
    }

    private function normaliseIdList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[,\s]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $ids = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $ids = array_merge($ids, $this->normaliseIdList($item));
                continue;
            }

            $id = (int) $item;

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function expandCategoryIds(array $catIds, DatabaseInterface $db): array
    {
        $catIds = array_values(array_unique(array_map('intval', $catIds)));

        if (!$catIds) {
            return [];
        }

        $query = $db->getQuery(true);
        $catIdKeys = $query->bindArray($catIds, ParameterType::INTEGER);

        $query
            ->select($db->quoteName('child.id'))
            ->from($db->quoteName('#__categories', 'parent'))
            ->join('INNER', $db->quoteName('#__categories', 'child') . ' ON ' . $db->quoteName('child.lft') . ' >= ' . $db->quoteName('parent.lft') . ' AND ' . $db->quoteName('child.rgt') . ' <= ' . $db->quoteName('parent.rgt'))
            ->where($db->quoteName('parent.id') . ' IN (' . implode(',', $catIdKeys) . ')')
            ->where($db->quoteName('child.extension') . ' = ' . $db->quote('com_content'));

        $db->setQuery($query);
        $expanded = array_map('intval', $db->loadColumn() ?: []);

        return array_values(array_unique(array_merge($catIds, $expanded)));
    }

    private function getCurrentCategoryId(Input $input, DatabaseInterface $db): int
    {
        if ($input->getCmd('option') !== 'com_content') {
            return 0;
        }

        $view = $input->getCmd('view');

        if ($view === 'category') {
            return (int) $input->getInt('id', 0);
        }

        if ($view === 'article') {
            $articleId = (int) $input->getInt('id', 0);

            if ($articleId <= 0) {
                return 0;
            }

            $query = $db->getQuery(true)
                ->select($db->quoteName('catid'))
                ->from($db->quoteName('#__content'))
                ->where($db->quoteName('id') . ' = :articleId')
                ->bind(':articleId', $articleId, ParameterType::INTEGER);

            $db->setQuery($query);

            return (int) $db->loadResult();
        }

        return 0;
    }

    private function applyOrdering(object $query, DatabaseInterface $db, string $ordering, bool $manualIds): void
    {
        if ($manualIds) {
            return;
        }

        switch ($ordering) {
            case 'created_desc':
                $query->order($db->quoteName('a.created') . ' DESC');
                break;

            case 'modified_desc':
                $query->order($db->quoteName('a.modified') . ' DESC');
                break;

            case 'hits_desc':
                $query->order($db->quoteName('a.hits') . ' DESC');
                break;

            case 'title_asc':
                $query->order($db->quoteName('a.title') . ' ASC');
                break;

            case 'ordering_asc':
                $query->order($db->quoteName('a.ordering') . ' ASC');
                break;

            case 'random':
                if (method_exists($query, 'rand')) {
                    $query->order($query->rand());
                } else {
                    $query->order($db->quoteName('a.id') . ' DESC');
                }
                break;

            case 'publish_up_desc':
            default:
                $query->order($db->quoteName('a.publish_up') . ' DESC');
                break;
        }

        $query->order($db->quoteName('a.id') . ' DESC');
    }

    private function sortRowsByManualIds(array $rows, array $articleIds): array
    {
        $positions = array_flip($articleIds);

        usort(
            $rows,
            static function ($a, $b) use ($positions): int {
                return ($positions[(int) $a->id] ?? PHP_INT_MAX) <=> ($positions[(int) $b->id] ?? PHP_INT_MAX);
            }
        );

        return $rows;
    }
}
