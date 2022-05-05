<?php
class Item {
    public $name;
    public $image;
    public $url;

    function displayThumbnail() {
        echo '<a href="'.$this->{'url'}.'">
        <img src="'.$this->{'image'}.'" title="'.$this->{'name'}.'">
        </a>';
    }
}
?>
