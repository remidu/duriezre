<?php
header("Content-Type:application/json");
require('../item.php');

function extract_game($node) {
    $game = new Item();
    $game->{'url'} = 'https://www.gamekult.com'.$node->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
    $game->{'image'} = str_replace('90_90','220_220',$node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('src')->textContent);
    $game->{'name'} = $node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('alt')->textContent;
    return $game;
}		

$payload = file_get_contents('https://www.gamekult.com/membre/1241161/collection/rechercher.html?&page=1');
$payload = str_replace('figure','h6',$payload);
$doc = new DomDocument();
$doc->loadHTML($payload, LIBXML_NOERROR);
$xpath = new DOMXPath($doc);
$list = $xpath->query('//h6');
for ($i = 0; $i < 3; $i++) {
    $games[$i] = extract_game($list->item($i));
}

echo json_encode($games);
?>
