<?php
header("Content-Type:application/json");
$config = include('../config.php');
require('../album.php');

function extract_album($user_album) {
    $album = new Album();
    $album->{'artist'} = $user_album->{'artist'}->{'name'};
    $album->{'name'} = $user_album->{'name'};
    $album->{'url'} = $user_album->{'url'};
    $album->{'image'} = $user_album->{'image'}[3]->{'#text'};
    return $album;
}

$payload = file_get_contents('http://ws.audioscrobbler.com/2.0/?method=user.gettopalbums&user=remidu&period=1month&limit=3&format=json&api_key='.$config['lastfm_api_key']);
$payload = json_decode($payload);
$albums = array_map('extract_album', $payload->{'topalbums'}->{'album'});

echo json_encode($albums);
?>
