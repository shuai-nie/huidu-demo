<?php

function allAdventFind($id)
{
    $data = model('Adsense')->allFind($id);
    return $data['title'];
}