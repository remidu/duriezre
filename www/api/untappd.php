<?php
header("Content-Type:application/json");
require('../item.php');

function extract_beer($node) {
    $beer = new Item();
    $beer->{'url'} = 'https://untappd.com'.$node->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
    $beer->{'image'} = $node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('data-original')->textContent;
    $beer->{'name'} = $node->childNodes->item(2)->childNodes->item(1)->childNodes->item(0)->textContent;
    return $beer;
}

$payload = file_get_contents('https://untappd.com/user/remidu/beers?sort=date');
$doc = new DomDocument();
$doc->loadHTML($payload, LIBXML_NOERROR);
$xpath = new DOMXPath($doc);
$list = $xpath->query('//*/div[@class="beer-item"]');
for ($i = 0; $i < 3; $i++) {
    $beers[$i] = extract_beer($list->item($i));
}

echo json_encode($beers);
?>
