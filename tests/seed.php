<?php
require (getenv('JOOMLA_ROOT') ?: getcwd()) . '/configuration.php';
$c = new JConfig;
$db = new PDO("mysql:host=$c->host;dbname=$c->db", $c->user, $c->password, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$p=$c->dbprefix;
function insertRow($db,$table,$row) { $s=$db->prepare('INSERT INTO '.$table.' (`'.implode('`,`',array_keys($row)).'`) VALUES ('.implode(',',array_fill(0,count($row),'?')).')'); $s->execute(array_values($row)); return $db->lastInsertId(); }
$cat=$db->query("SELECT id FROM {$p}categories WHERE extension='com_content' AND published=1 ORDER BY id LIMIT 1")->fetchColumn();
if (!$db->query("SELECT id FROM {$p}content WHERE alias='articlex-smoke-1'")->fetchColumn()) {
 foreach ([1,2,3] as $i) {
  insertRow($db,$p.'content',['title'=>'ArticleX Smoke '.$i,'alias'=>'articlex-smoke-'.$i,'introtext'=>'<p>ArticleX functional fixture '.$i.'</p>','fulltext'=>'','state'=>1,'catid'=>$cat,'modified'=>'2026-01-01 12:00:00','created'=>'2026-01-01 12:00:00','publish_up'=>'2026-01-01 12:00:00','images'=>json_encode(['image_intro'=>'images/joomla_black.png','image_intro_alt'=>'Fixture '.$i]),'urls'=>'{}','attribs'=>'{}','metakey'=>'','metadesc'=>'','metadata'=>'{}','access'=>1,'language'=>'*','featured'=>1]);
 }
}
foreach (['default','grid','list','media','featured','magazine','carousel'] as $layout) {
 $title='ArticleX Test '.$layout;
 $s=$db->prepare("SELECT id FROM {$p}modules WHERE title=? AND module='mod_jtarticlexpro'"); $s->execute([$title]);
 $id=$s->fetchColumn();
 if (!$id) {
  $id=insertRow($db,$p.'modules',['title'=>$title,'note'=>'Local onboarding fixture','position'=>'bottom-a','published'=>1,'module'=>'mod_jtarticlexpro','access'=>1,'showtitle'=>1,'params'=>json_encode(['layout'=>$layout,'count'=>3,'columns'=>3,'autoplay'=>0,'show_schema'=>1]),'client_id'=>0,'language'=>'*','content'=>'']);
  insertRow($db,$p.'modules_menu',['moduleid'=>$id,'menuid'=>0]);
 }
}
echo "Seed ready: 3 articles and 7 layouts (idempotent).\n";
