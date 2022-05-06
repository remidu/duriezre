<?php
header("Content-Type:application/json");
$config = include('../config.php');
require('../item.php');

function extract_show($member_show, $show_detail) {
    $show = new Item();
    $show->{'url'} = 'https://www.betaseries.com/serie/'.$show_detail->{'slug'};
    $show->{'image'} = $member_show->{'images'}->{'poster'};
    $show->{'name'} = $member_show->{'title'};
    return $show;
}

function extract_id($member_show) {
    return $member_show->{'id'};
}

$key = $config['betaseries_api_key'];
$shows_payload = file_get_contents('https://api.betaseries.com/shows/member?id=23559&order=last_seen&summary=true&limit=3&key='.$key);
$shows_payload = json_decode($shows_payload);
$ids = implode(',', array_map('extract_id', $shows_payload->{'shows'}));
$details_payload = file_get_contents('https://api.betaseries.com/shows/display?id='.$ids.'&key='.$key);
$details_payload = json_decode($details_payload);
$shows = array_map('extract_show', $shows_payload->{'shows'}, $details_payload->{'shows'});

echo json_encode($shows);
?>
