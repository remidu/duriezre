<?php
header("Content-Type:application/json");
require('../item.php');

function extract_book($node) {
    $book = new Item();
    $book->{'url'} = 'https://leagueofcomicgeeks.com'.$node->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('href')->textContent;
    $book->{'image'} = str_replace('medium','large',$node->childNodes->item(1)->childNodes->item(1)->childNodes->item(1)->attributes->getNamedItem('data-src')->textContent);
    $book->{'name'} = $node->childNodes->item(7)->childNodes->item(1)->textContent;
    return $book;
}

$payload = file_get_contents('https://leagueofcomicgeeks.com/comic/get_comics?user_id=43509&list=1&date_type=recent&order=pulls');
$payload = json_decode($payload);
$doc = new DomDocument();
$doc->loadHTML($payload->{'list'}, LIBXML_NOERROR);
$xpath = new DOMXPath($doc);
$list = $xpath->query('//li');
for ($i = 0; $i < 3; $i++) {
    $comics[$i] = extract_book($list->item($i));
}

echo json_encode($comics);
?>
