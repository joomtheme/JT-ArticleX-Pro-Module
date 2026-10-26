<?php
/** Run against an isolated, installed Joomla site: php /path/to/tests/integration.php. */
define('_JEXEC', 1);
define('JPATH_BASE', getenv('JOOMLA_ROOT') ?: getcwd());
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
require JPATH_BASE . '/includes/defines.php';
require JPATH_BASE . '/includes/framework.php';

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

$container = Factory::getContainer();
$container->alias('session', 'session.web.site')->alias('session.web', 'session.web.site')
    ->alias(Joomla\Session\SessionInterface::class, 'session.web.site');
$app = $container->get(SiteApplication::class);
Factory::$application = $app;
$app->createExtensionNamespaceMap();
$app->loadIdentity(new User());
(new ReflectionMethod($app, 'initialiseApp'))->invoke($app);
require_once JPATH_BASE . '/modules/mod_jtarticlexpro/src/Helper/ArticleXProHelper.php';
$db = $container->get(DatabaseInterface::class);
$helper = new JoomTheme\Module\ArticleXPro\Site\Helper\ArticleXProHelper();
$helper->setDatabase($db);
$checks = 0;
function check(bool $ok, string $message): void
{
    global $checks;
    if (!$ok) {
        throw new RuntimeException($message);
    }
    $checks++;
}
function articles(array $ids, array $options = []): array
{
    global $helper, $app;
    return $helper->getArticles(new Registry(array_replace([
        'article_ids' => implode(',', $ids), 'count' => 48, 'language_filter' => 0,
    ], $options)), $app, $app->getInput());
}
function imageData(int $id, string $json): void
{
    global $db;
    $row = (object) ['id' => $id, 'images' => $json];
    $db->updateObject('#__content', $row, 'id');
}

$db->transactionStart();
try {
    $db->setQuery($db->getQuery(true)->select('*')->from($db->quoteName('#__content'))->order('id'), 0, 1);
    $template = $db->loadObject();
    if (!$template) {
        throw new RuntimeException('Create a test article before running this isolated-site test.');
    }
    $db->setQuery($db->getQuery(true)->select('id')->from($db->quoteName('#__categories'))
        ->where("extension = 'com_content' AND published = 1 AND access = 1"), 0, 1);
    $catid = (int) $db->loadResult();
    check($catid > 0, 'A public category is required.');
    $ids = [];
    for ($i = 0; $i < 6; $i++) {
        $row = clone $template;
        unset($row->id);
        $row->title = 'ArticleX regression ' . $i;
        $row->alias = 'articlex-regression-' . bin2hex(random_bytes(6));
        $row->state = 1;
        $row->catid = $catid;
        $row->access = 1;
        $row->language = '*';
        $row->created = '2090-01-01 00:00:0' . $i;
        $row->publish_up = '2020-01-01 00:00:00';
        $row->publish_down = null;
        $row->featured = 0;
        $row->images = $i < 3 ? json_encode(['image_intro' => 'images/test-' . $i . '.jpg']) : '{}';
        $db->insertObject('#__content', $row, 'id');
        $ids[] = (int) $row->id;
    }
    foreach (['intro', 'fulltext', 'auto'] as $source) {
        $key = $source === 'fulltext' ? 'image_fulltext' : 'image_intro';
        foreach ([0, JSON_PRETTY_PRINT] as $format) {
            imageData($ids[0], json_encode([$key => 'images/test.jpg', $key . '_alt' => 'Selected alt'], $format));
            $items = articles([$ids[0]], ['only_with_image' => 1, 'image_source' => $source]);
            check(count($items) === 1, "$source: compact/pretty JSON accepted");
            check($items[0]->image_alt === 'Selected alt', "$source: selected alt preserved");
        }
    }
    imageData($ids[0], '{"image_\\u0069ntro":"images/unicode.jpg"}');
    check(count(articles([$ids[0]], ['only_with_image' => 1])) === 1, 'Escaped JSON keys accepted');
    foreach (['{}', 'null', 'broken JSON', '{"image_intro":[]}', '{"image_intro":false}', '{"image_intro":"   "}'] as $json) {
        imageData($ids[0], $json);
        check(articles([$ids[0]], ['only_with_image' => 1]) === [], 'Invalid or empty image excluded: ' . $json);
    }
    imageData($ids[0], json_encode(['image_intro' => '  ', 'image_intro_alt' => 'Wrong intro alt',
        'image_fulltext' => ' images/full.jpg ', 'image_fulltext_alt' => 'Full alt']));
    $item = articles([$ids[0]], ['only_with_image' => 1, 'image_source' => 'auto'])[0];
    check($item->image === 'images/full.jpg' && $item->image_alt === 'Full alt', 'Fallback pairs image and alt and trims source');
    imageData($ids[0], json_encode(['image_intro' => 'images/intro.jpg', 'image_fulltext_alt' => 'Wrong fulltext alt']));
    check(articles([$ids[0]], ['image_source' => 'auto'])[0]->image_alt === 'ArticleX regression 0', 'Missing selected alt uses title');
    $items = articles($ids, ['count' => 2, 'only_with_image' => 1]);
    check(array_column($items, 'id') === [$ids[0], $ids[1]], 'Manual order and count after filtering');
    $items = articles([], ['count' => 3, 'only_with_image' => 1, 'ordering' => 'created_desc']);
    check(array_column($items, 'id') === [$ids[2], $ids[1], $ids[0]], 'Automatic count filled beyond first three imageless rows');
    $items = articles($ids, ['count' => 2, 'only_with_image' => 1, 'ordering' => 'random']);
    check(count($items) === 2, 'Random option preserves manual selection');
    $items = articles([], ['count' => 2, 'only_with_image' => 1, 'ordering' => 'random']);
    check(count($items) === 2 && count(array_unique(array_column($items, 'id'))) === 2, 'Random results contain enough distinct images');

    foreach ([0 => [null, null], 1 => ['2099-01-01 00:00:00', null], 2 => [null, '2001-01-01 00:00:00']] as $i => [$up, $down]) {
        $row = (object) ['id' => $ids[$i], 'featured' => 1];
        $db->updateObject('#__content', $row, 'id');
        $row = (object) ['content_id' => $ids[$i], 'ordering' => $i, 'featured_up' => $up, 'featured_down' => $down];
        $db->insertObject('#__content_frontpage', $row);
    }
    check(array_column(articles($ids, ['featured' => 1]), 'id') === [$ids[0]], 'Featured-only respects future and expired schedules');
    check(array_column(articles($ids, ['featured' => -1]), 'id') === array_slice($ids, 1), 'Excluded-featured includes inactive schedules');
    check(array_column(articles(array_slice($ids, 0, 3)), 'featured') === [1, 0, 0], 'Featured presentation follows schedule');

    $params = new Registry(['autoplay' => 0, 'count' => 3]);
    $items = articles(array_slice($ids, 0, 3));
    $moduleId = 'articlex-fallback-test';
    $headingLevel = 'h3';
    $ratioClass = 'ratio-16x9';
    $columns = 3;
    $app->loadDocument(new Joomla\CMS\Document\HtmlDocument());
    $wa = $app->getDocument()->getWebAssetManager();
    $wa->getRegistry()->get('script', 'bootstrap.carousel');
    $wa->getRegistry()->remove('script', 'bootstrap.carousel');
    ob_start();
    require JPATH_BASE . '/modules/mod_jtarticlexpro/tmpl/carousel.php';
    $html = ob_get_clean();
    check(str_contains($html, 'jt-articlex-pro-grid'), 'Missing carousel asset falls back to grid');
    check(!str_contains($html, 'carousel-item'), 'Fallback does not hide secondary slides');
    foreach ($items as $item) {
        check(str_contains($html, $item->title), 'Fallback renders every article');
    }
    echo json_encode(['checks' => $checks, 'status' => 'passed'], JSON_PRETTY_PRINT) . "\n";
} finally {
    $db->transactionRollback();
}
