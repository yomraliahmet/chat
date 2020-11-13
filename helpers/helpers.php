<?php

function makeImageFromName($name){
    $userImage = "";
    $shortName = "";

    $names = explode(" ", $name);

    foreach ($names  as $w){
        $shortName .= $w[0];
    }

    $userImage = '<div class="name-image bg-primary">'.substr($shortName,0,2).'</div>';

    return $userImage;

}
