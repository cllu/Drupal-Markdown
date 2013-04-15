<?php

include_once 'phpflickr/phpFlickr.php';

class Flickr {

        private $api_key = '4231c9fc6d682d17c618091def128f87';
        private $api_secret = 'f15dfdb9e761282c';
        private $api_token = '72157633245931161-c47f26f9fc81772a';
        private $f = null;

    function __construct() {
        $this->f =& new phpFlickr($this->api_key, $this->api_secret);
        $this->f->setToken($this->api_token);
    }

    public function get_photo_url($photo_id, $size = "medium") {
        if ($photo = $this->f->photos_getInfo($photo_id)) {
            return $this->f->buildPhotoURL($photo, $size);
        } else {
            return False;
        }
    }

}

//$flickr = new Flickr();
//print_r($flickr->get_photo_url(8626742647));
